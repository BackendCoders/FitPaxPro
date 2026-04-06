<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminGymController extends Controller
{
    public function index(Request $request): View
    {
        $gyms = Gym::query()
            ->with('owner:id,name,email')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin::gym.index', $this->buildBaseViewData($request) + [
            'gyms' => $gyms,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin::gym.create', $this->buildBaseViewData($request) + [
            'owners' => $this->ownerOptions(),
            'statusOptions' => $this->statusOptions(),
            'formAction' => route('admin.gym.store'),
            'httpMethod' => 'POST',
            'gym' => new Gym([
                'search_radius_km' => 10,
                'is_sponsored' => 0,
                'rating_avg' => 0,
                'status' => 'pending',
                'is_verified' => 0,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedPayload($request);

        Gym::query()->create($payload);

        return redirect()
            ->route('admin.gym.index')
            ->with('success', 'Gym created successfully.');
    }

    public function edit(Request $request, Gym $gym): View
    {
        return view('admin::gym.edit', $this->buildBaseViewData($request) + [
            'owners' => $this->ownerOptions(),
            'statusOptions' => $this->statusOptions(),
            'formAction' => route('admin.gym.update', $gym),
            'httpMethod' => 'PUT',
            'gym' => $gym,
        ]);
    }

    public function update(Request $request, Gym $gym): RedirectResponse
    {
        $payload = $this->validatedPayload($request, $gym);

        $gym->update($payload);

        return redirect()
            ->route('admin.gym.index')
            ->with('success', 'Gym updated successfully.');
    }

    public function destroy(Gym $gym): RedirectResponse
    {
        $gym->delete();

        return redirect()
            ->route('admin.gym.index')
            ->with('success', 'Gym deleted successfully.');
    }

    protected function validatedPayload(Request $request, ?Gym $gym = null): array
    {
        $validated = $request->validate([
            'owner_id' => ['required', 'string', Rule::exists('users', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('gyms', 'slug')->ignore($gym?->id),
            ],
            'description' => ['nullable', 'string'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'logo_file' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'intro_video_url' => ['nullable', 'string', 'max:500'],
            'intro_video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm', 'max:51200'],
            'address' => ['required', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'search_radius_km' => ['nullable', 'integer', 'min:0'],
            'is_sponsored' => ['required', 'boolean'],
            'member_count_limit' => ['nullable', 'integer', 'min:0'],
            'rating_avg' => ['nullable', 'numeric', 'between:0,5'],
            'status' => ['required', Rule::in($this->statusOptions())],
            'is_verified' => ['required', 'boolean'],
        ]);

        $validated['is_sponsored'] = (bool) $validated['is_sponsored'];
        $validated['is_verified'] = (bool) $validated['is_verified'];

        if ($request->hasFile('logo_file')) {
            $validated['logo_path'] = 'storage/'.$request->file('logo_file')->store('gyms/logos', 'public');
        }

        if ($request->hasFile('intro_video_file')) {
            $validated['intro_video_url'] = 'storage/'.$request->file('intro_video_file')->store('gyms/videos', 'public');
        }

        unset($validated['logo_file'], $validated['intro_video_file']);

        return $validated;
    }

    protected function ownerOptions()
    {
        return User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    protected function statusOptions(): array
    {
        return ['pending', 'active', 'suspended', 'rejected'];
    }

    protected function buildBaseViewData(Request $request): array
    {
        $admin = $request->user('admin');

        return [
            'admin' => $admin,
            'adminInitials' => collect(explode(' ', trim((string) $admin->name)))
                ->filter()
                ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
                ->take(2)
                ->implode('') ?: 'SA',
            'navigation' => $this->navigation(),
            'currentRouteName' => optional($request->route())->getName(),
        ];
    }

    protected function navigation(): array
    {
        return [
            [
                'label' => 'Dashboard Overview',
                'route' => 'admin.dashboard',
                'type' => 'link',
                'icon' => 'dashboard',
            ],
            [
                'label' => 'Gym Operations',
                'type' => 'dropdown',
                'icon' => 'gym',
                'items' => [
                    ['label' => 'Add Gym', 'route' => 'admin.gym.create'],
                    ['label' => 'Manage Gyms', 'route' => 'admin.gym.index'],
                    ['label' => 'Membership Plans', 'route' => 'admin.gym.fee-plans'],
                    ['label' => 'Trainers', 'route' => 'admin.dashboard'],
                    ['label' => 'Attendance / Bookings', 'route' => 'admin.gym.attendance'],
                    ['label' => 'Gym Enquiries', 'route' => 'admin.gym.enquiry'],
                    ['label' => 'Gallery Media', 'route' => 'admin.gym.gallery-media'],
                    ['label' => 'Gym Reviews', 'route' => 'admin.gym.reviews'],
                ],
            ],
            // [
            //     'label' => 'Existing Modules',
            //     'route' => 'admin.gym.index',
            //     'type' => 'link',
            //     'icon' => 'modules',
            // ],
            [
                'label' => 'Reports',
                'route' => 'admin.gym.reviews',
                'type' => 'link',
                'icon' => 'reports',
            ],
            [
                'label' => 'Settings',
                'route' => 'admin.dashboard',
                'type' => 'link',
                'icon' => 'settings',
            ],
        ];
    }
}
