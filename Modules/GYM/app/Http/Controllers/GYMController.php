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
            $query->where('is_verified', $request->status == 'verified');
        }

        if ($request->filled('sponsored')) {
            $query->where('is_sponsored', true);
        }

        if ($request->filled('plan_id')) {
            $query->where('platform_plan_id', $request->plan_id);
        }

        $gyms = $query->get();
        $platformPlans = \App\Models\PlatformSubscriptionPlan::all();
        
        return view('gym::index', compact('gyms', 'platformPlans'));
    }

    /**
     * Show the form for creating a new gym.
     */
    public function create()
    {
        $templates = \App\Models\MembershipPlanTemplate::where('is_active', true)->get();
        $platformPlans = \DB::table('platform_subscription_plans')->get(); // Using DB for now or create model
        return view('gym::create', compact('templates', 'platformPlans'));
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
            'template_ids' => 'nullable|array',
            'template_ids.*' => 'exists:membership_plan_templates,id',
            'custom_plans' => 'nullable|array',
            'custom_plans.*.name' => 'required|string',
            'custom_plans.*.price' => 'required|numeric',
        ];

        $dynamicRules = \App\Models\Gym::getCustomFieldRules(\App\Models\Gym::class);
        $request->validate(array_merge($rules, $dynamicRules));

        $data = $request->except(['template_ids', 'custom_plans']);
        $data['owner_id'] = auth()->id(); 

        $gym = $this->gymRepository->createGym($data);

        // 0. Handle Primary Image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gyms', 'public');
            $gym->update(['image' => $path]);
        }

        // 0.1 Handle Gallery
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('gyms/gallery', 'public');
                \App\Models\GymGalleryMedia::create([
                    'gym_id' => $gym->id,
                    'file_path' => $path,
                    'file_type' => 'image',
                    'file_size' => $file->getSize(),
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }
        }

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
            foreach ($request->custom_plans as $cp) {
                \App\Models\GymFeePlan::create([
                    'gym_id' => $gym->id,
                    'name' => $cp['name'],
                    'price' => $cp['price'],
                    'offer_price' => $cp['offer_price'] ?? null,
                    'duration_months' => $cp['duration_months'] ?? 1,
                    'includes_diet_plan' => isset($cp['includes_diet_plan']),
                    'includes_trainer' => isset($cp['includes_trainer']),
                    'is_active' => true,
                ]);
            }
        }

        if ($request->has('custom_fields')) {
            $gym->saveCustomFields($request->custom_fields);
        }

        return redirect()->route('gym.index')->with('success', 'Gym created successfully with selected plans!');
    }

    /**
     * Show media management for a gym.
     */
    public function mediaSettings($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        $gym->load('galleryMedia');
        return view('gym::media_settings', compact('gym'));
    }

    /**
     * Update media for a gym.
     */
    public function updateMedia(Request $request, $uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gyms', 'public');
            $gym->update(['image' => $path]);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('gyms/gallery', 'public');
                \App\Models\GymGalleryMedia::create([
                    'gym_id' => $gym->id,
                    'file_path' => $path,
                    'file_type' => 'image',
                    'file_size' => $file->getSize(),
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return back()->with('success', 'Media portfolio updated successfully.');
    }

    /**
     * Show the form for editing the specified gym.
     */
    public function edit($uuid)
    {
        $gym = $this->gymRepository->getGymById($uuid);
        return view('gym::edit', compact('gym'));
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
        $request->validate(array_merge($rules, $dynamicRules));

        $this->gymRepository->updateGym($uuid, $request->all());

        if ($request->has('custom_fields')) {
            $gym->saveCustomFields($request->custom_fields);
        }

        return redirect()->route('gym.index')->with('success', 'Gym updated successfully!');
    }

    /**
     * Remove the specified gym from storage.
     */
    public function destroy($uuid)
    {
        $this->gymRepository->deleteGym($uuid);
        return redirect()->route('gym.index')->with('success', 'Gym deleted successfully!');
    }
}
