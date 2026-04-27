<?php

namespace Modules\GYM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;

class GYMController extends Controller
{
    private $gymRepository;

    public function __construct(GymRepositoryInterface $gymRepository)
    {
        $this->gymRepository = $gymRepository;
    }

    /**
     * Display a listing of gyms.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Gym::with(['owner', 'platformPlan'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'verified') {
                $query->where('is_verified', true);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('sponsored')) {
            $query->where('is_sponsored', true);
        }

        if ($request->filled('plan_id')) {
            $query->where('platform_plan_id', $request->plan_id);
        }

        $gyms = $query->paginate(12)->withQueryString();
        $platformPlans = \App\Models\PlatformSubscriptionPlan::all();
        
        return view('gym::index', compact('gyms', 'platformPlans'));
    }

    /**
     * Show the form for creating a new gym.
     */
    public function create()
    {
        $templates = \App\Models\MembershipPlanTemplate::where('is_active', true)->get();
        $platformPlans = \DB::table('platform_subscription_plans')->get(); 
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('gym::create', compact('templates', 'platformPlans', 'categories'));
    }

    /**
     * Store a newly created gym in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:gyms,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'member_count_limit' => 'nullable|integer',
            'platform_plan_id' => 'nullable|exists:platform_subscription_plans,id',
            'image' => 'nullable|image|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:5120',
            'youtube_links' => 'nullable|array',
            'youtube_links.*' => 'nullable|url',
            'template_ids' => 'nullable|array',
            'template_ids.*' => 'exists:membership_plan_templates,id',
            'custom_plans' => 'nullable|array',
            'custom_plans.*.name' => 'required|string',
            'custom_plans.*.price' => 'required|numeric',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
        ];

        $dynamicRules = \App\Models\Gym::getCustomFieldRules(\App\Models\Gym::class);
        $request->validate(array_merge($rules, $dynamicRules));

        $data = $request->except(['template_ids', 'custom_plans', 'category_ids', 'custom_fields', 'image', 'gallery', 'youtube_links']);
        $data['owner_id'] = auth()->id(); 

        $gym = $this->gymRepository->createGym(
            $data,
            $request->file('image'),
            $request->file('gallery', []),
            $request->input('youtube_links', [])
        );

        // 1. Link selected master templates
        if ($request->has('template_ids')) {
            $templates = \App\Models\MembershipPlanTemplate::whereIn('id', $request->template_ids)->get();
            foreach ($templates as $template) {
                \App\Models\GymFeePlan::create([
                    'gym_id' => $gym->id,
                    'name' => $template->name,
                    'tagline' => $template->tagline,
                    'description' => $template->description,
                    'features' => $template->features,
                    'price' => $template->price,
                    'offer_price' => $template->offer_price,
                    'duration_months' => $template->duration_months,
                    'includes_diet_plan' => $template->includes_diet_plan,
                    'includes_trainer' => $template->includes_trainer,
                    'is_active' => true,
                ]);
            }
        }

        // 2. Create custom plans
        if ($request->has('custom_plans')) {
            foreach ($request->custom_plans as $index => $cp) {
                $planData = [
                    'gym_id' => $gym->id,
                    'name' => $cp['name'],
                    'tagline' => $cp['tagline'] ?? null,
                    'price' => $cp['price'],
                    'offer_price' => $cp['offer_price'] ?? null,
                    'duration_months' => $cp['duration_months'] ?? 1,
                    'includes_diet_plan' => isset($cp['includes_diet_plan']),
                    'includes_trainer' => isset($cp['includes_trainer']),
                    'is_active' => true,
                ];

                // Handle Plan Image
                if ($request->hasFile("custom_plans.{$index}.image")) {
                    $planData['image'] = $request->file("custom_plans.{$index}.image")->store('gyms/plans', 'public');
                }

                \App\Models\GymFeePlan::create($planData);
            }
        }

        if ($request->has('custom_fields')) {
            $gym->saveCustomFields($request->custom_fields);
        }

        if ($request->has('category_ids')) {
            $gym->categories()->sync($request->category_ids);
        }

        return redirect()->route('gym.index')->with('success', 'Gym created successfully with selected plans!');
    }

    /**
     * Show media management for a gym.
     */
    public function mediaSettings($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        $gym->load(['galleryMedia', 'videoMedia']);
        return view('gym::media_settings', compact('gym'));
    }

    /**
     * Show analytics for a gym.
     */
    public function analytics($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        
        $stats = [
            'active_members' => $gym->subscriptions()->where('status', 'active')->count(),
            'total_revenue' => $gym->subscriptions()->sum('amount_paid'),
            'pending_enquiries' => $gym->enquiries()->count(), // Filter by status if available
            'avg_attendance_weekly' => round($gym->attendanceLogs()->where('check_in_time', '>=', now()->subDays(7))->count() / 7, 1),
            'revenue_this_month' => $gym->subscriptions()
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount_paid'),
            'new_signups_30d' => $gym->subscriptions()
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
        ];

        // Simplified data for charts
        $revenueTrend = $gym->subscriptions()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(amount_paid) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $attendanceTrend = $gym->attendanceLogs()
            ->where('check_in_time', '>=', now()->subDays(30))
            ->selectRaw('DATE(check_in_time) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('gym::analytics', compact('gym', 'stats', 'revenueTrend', 'attendanceTrend'));
    }

    /**
     * Show members (subscribers) for a gym.
     */
    public function members($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        $members = $gym->subscriptions()->with(['user.roles', 'feePlan'])->latest()->paginate(20);
        return view('gym::members', compact('gym', 'members'));
    }

    /**
     * Update media for a gym.
     */
    public function updateMedia(Request $request, $uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        $request->validate([
            'image' => 'nullable|image|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:5120',
            'youtube_links' => 'nullable|array',
            'youtube_links.*' => 'nullable|url',
        ]);
        
        $this->gymRepository->uploadNodeAssets(
            $gym,
            $request->file('image'),
            $request->file('gallery', []),
            $request->input('youtube_links', [])
        );

        return back()->with('success', 'Media portfolio updated successfully.');
    }

    /**
     * Remove the specified media from storage.
     */
    public function destroyMedia($id)
    {
        $media = \App\Models\GymGalleryMedia::findOrFail($id);
        
        // Delete file from storage
        if ($media->file_path && \Storage::disk('public')->exists($media->file_path)) {
            \Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return back()->with('success', 'Media removed successfully.');
    }

    /**
     * Show the form for editing the specified gym.
     */
    public function edit($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        $gym->load('categories');
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name', 'asc')->get();
        return view('gym::edit', compact('gym', 'categories'));
    }

    /**
     * Update the specified gym in storage.
     */
    public function update(Request $request, $uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:gyms,email,' . $gym->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ];

        $dynamicRules = \App\Models\Gym::getCustomFieldRules(\App\Models\Gym::class);
        $request->validate(array_merge($rules, $dynamicRules, [
            'image' => 'nullable|image|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:5120',
            'youtube_links' => 'nullable|array',
            'youtube_links.*' => 'nullable|url',
        ]));

        $this->gymRepository->updateGym(
            $uuid,
            $request->except(['category_ids', 'custom_fields', 'image', 'gallery', 'youtube_links']),
            $request->file('image'),
            $request->file('gallery', []),
            $request->input('youtube_links', [])
        );

        if ($request->has('custom_fields')) {
            $gym->saveCustomFields($request->custom_fields);
        }

        if ($request->has('category_ids')) {
            $gym->categories()->sync($request->category_ids);
        } else {
            $gym->categories()->sync([]);
        }

        return redirect()->route('gym.index')->with('success', 'Gym updated successfully!');
    }

    /**
     * Toggle the operational status of a gym.
     */
    public function toggleStatus($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        $gym->status = ($gym->status == 'active') ? 'inactive' : 'active';
        $gym->save();

        return back()->with('success', 'Operational focus ' . strtoupper($gym->status) . ' for ' . $gym->name);
    }
}
