<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Gym;
use App\Models\GymEnquiry;
use App\Models\GymFeePlan;
use App\Models\GymGalleryMedia;
use App\Models\GymReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
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

    public function feePlans(Request $request): View
    {
        $columns = Schema::hasTable('gym_fee_plans')
            ? Schema::getColumnListing('gym_fee_plans')
            : [];

        $query = GymFeePlan::query()
            ->with('gym:id,name');

        if (in_array('created_at', $columns, true)) {
            $query->latest('created_at');
        } else {
            $query->latest((new GymFeePlan())->getKeyName());
        }

        $feePlans = $query
            ->paginate(12)
            ->withQueryString();

        $approvalStates = $feePlans->getCollection()
            ->mapWithKeys(fn (GymFeePlan $plan) => [$plan->getKey() => $this->approvalState($plan)])
            ->all();

        return view('admin::gym.fee-plans', $this->buildBaseViewData($request) + [
            'feePlans' => $feePlans,
            'feePlanColumns' => $columns,
            'approvalStates' => $approvalStates,
        ]);
    }

    public function feePlanView(Request $request, GymFeePlan $feePlan): View
    {
        $columns = Schema::hasTable('gym_fee_plans')
            ? Schema::getColumnListing('gym_fee_plans')
            : [];

        $feePlan->loadMissing('gym:id,name');

        return view('admin::gym.fee-plan-view', $this->buildBaseViewData($request) + [
            'feePlan' => $feePlan,
            'feePlanColumns' => $columns,
            'approvalState' => $this->approvalState($feePlan),
            'canModerateFeePlan' => $this->isSuperAdmin($request),
        ]);
    }

    public function approveFeePlan(Request $request, GymFeePlan $feePlan): RedirectResponse
    {
        return $this->updateFeePlanApprovalFromRequest($request, $feePlan, 'approved');
    }

    public function disapproveFeePlan(Request $request, GymFeePlan $feePlan): RedirectResponse
    {
        return $this->updateFeePlanApprovalFromRequest($request, $feePlan, 'disapproved');
    }

    public function attendance(Request $request): View
    {
        $attendanceColumns = Schema::hasTable('attendance_logs')
            ? Schema::getColumnListing('attendance_logs')
            : [];

        $query = AttendanceLog::query()
            ->with(['user:id,name,email', 'gym:id,name']);

        if (in_array('created_at', $attendanceColumns, true)) {
            $query->latest('created_at');
        } else {
            $query->latest((new AttendanceLog())->getKeyName());
        }

        $attendanceLogs = $query
            ->paginate(12)
            ->withQueryString();

        return view('admin::gym.attendance', $this->buildBaseViewData($request) + [
            'attendanceLogs' => $attendanceLogs,
            'attendanceColumns' => $attendanceColumns,
        ]);
    }

    public function attendanceView(Request $request, AttendanceLog $attendance): View
    {
        $attendanceColumns = Schema::hasTable('attendance_logs')
            ? Schema::getColumnListing('attendance_logs')
            : [];

        $attendance->loadMissing(['user:id,name,email', 'gym:id,name']);

        return view('admin::gym.attendance-view', $this->buildBaseViewData($request) + [
            'attendance' => $attendance,
            'attendanceColumns' => $attendanceColumns,
        ]);
    }

    public function enquiries(Request $request): View
    {
        $enquiryColumns = $this->enquiryColumns();

        $query = GymEnquiry::query()
            ->with(['user:id,name,email', 'gym:id,name']);

        if (in_array('created_at', $enquiryColumns, true)) {
            $query->latest('created_at');
        } else {
            $query->latest((new GymEnquiry())->getKeyName());
        }

        $enquiries = $query
            ->paginate(12)
            ->withQueryString();

        return view('admin::gym.enquiry', $this->buildBaseViewData($request) + [
            'enquiries' => $enquiries,
            'enquiryColumns' => $enquiryColumns,
        ]);
    }

    public function enquiryView(Request $request, GymEnquiry $enquiry): View
    {
        $enquiry->loadMissing(['user:id,name,email', 'gym:id,name']);

        return view('admin::gym.enquiry-view', $this->buildBaseViewData($request) + [
            'enquiry' => $enquiry,
            'enquiryColumns' => $this->enquiryColumns(),
        ]);
    }

    public function editEnquiry(Request $request, GymEnquiry $enquiry): View
    {
        $editableColumns = $this->editableEnquiryColumns();

        return view('admin::gym.enquiry-edit', $this->buildBaseViewData($request) + [
            'enquiry' => $enquiry,
            'editableColumns' => $editableColumns,
            'columnTypes' => $this->enquiryColumnTypes($editableColumns),
            'formAction' => route('admin.gym.enquiry.update', $enquiry),
        ]);
    }

    public function updateEnquiry(Request $request, GymEnquiry $enquiry): RedirectResponse
    {
        $rules = $this->enquiryValidationRules();
        $payload = $request->validate($rules);

        foreach ($payload as $column => $value) {
            $type = Schema::getColumnType('gym_enquiries', $column);

            if ($type === 'boolean') {
                $payload[$column] = (bool) $value;
            }
        }

        $enquiry->update($payload);

        return redirect()
            ->route('admin.gym.enquiry')
            ->with('success', 'Gym enquiry updated successfully.');
    }

    public function destroyEnquiry(GymEnquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()
            ->route('admin.gym.enquiry')
            ->with('success', 'Gym enquiry deleted successfully.');
    }

    public function galleryMedia(Request $request): View
    {
        $galleryColumns = Schema::hasTable('gym_gallery_media')
            ? Schema::getColumnListing('gym_gallery_media')
            : [];

        $query = GymGalleryMedia::query()
            ->with('gym:id,name');

        if (in_array('created_at', $galleryColumns, true)) {
            $query->latest('created_at');
        } else {
            $query->latest((new GymGalleryMedia())->getKeyName());
        }

        $galleryItems = $query
            ->paginate(12)
            ->withQueryString();

        return view('admin::gym.gallery-media', $this->buildBaseViewData($request) + [
            'galleryItems' => $galleryItems,
            'galleryColumns' => $galleryColumns,
        ]);
    }

    public function galleryMediaView(Request $request, GymGalleryMedia $galleryMedia): View
    {
        $galleryMedia->loadMissing('gym:id,name');

        return view('admin::gym.gallery-media-view', $this->buildBaseViewData($request) + [
            'galleryMedia' => $galleryMedia,
            'galleryColumns' => Schema::hasTable('gym_gallery_media')
                ? Schema::getColumnListing('gym_gallery_media')
                : [],
        ]);
    }

    public function reviews(Request $request): View
    {
        $reviewColumns = Schema::hasTable('gym_reviews')
            ? Schema::getColumnListing('gym_reviews')
            : [];

        $query = GymReview::query()
            ->with(['gym:id,name', 'user:id,name,email']);

        $ratingColumn = $this->reviewSortColumn($reviewColumns);
        if ($ratingColumn !== null) {
            $query->orderByDesc($ratingColumn);
        }

        if (in_array('created_at', $reviewColumns, true)) {
            $query->orderByDesc('created_at');
        }

        $reviews = $query
            ->paginate(12)
            ->withQueryString();

        return view('admin::gym.reviews', $this->buildBaseViewData($request) + [
            'reviews' => $reviews,
            'reviewColumns' => $reviewColumns,
            'reviewSortColumn' => $ratingColumn,
        ]);
    }

    public function reviewView(Request $request, GymReview $review): View
    {
        $review->loadMissing(['gym:id,name', 'user:id,name,email']);

        return view('admin::gym.review-view', $this->buildBaseViewData($request) + [
            'review' => $review,
            'reviewColumns' => Schema::hasTable('gym_reviews')
                ? Schema::getColumnListing('gym_reviews')
                : [],
        ]);
    }

    public function trainers(Request $request): View
    {
        $trainerColumns = Schema::hasTable('users')
            ? Schema::getColumnListing('users')
            : [];

        $trainers = User::query()
            ->where('user_type', 2)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin::gym.trainers', $this->buildBaseViewData($request) + [
            'trainers' => $trainers,
            'trainerColumns' => $trainerColumns,
        ]);
    }

    public function editTrainer(Request $request, User $trainer): View
    {
        abort_unless((int) $trainer->user_type === 2, 404, 'Trainer not found.');

        return view('admin::gym.trainer-edit', $this->buildBaseViewData($request) + [
            'trainer' => $trainer,
            'formAction' => route('admin.gym.trainers.update', $trainer),
        ]);
    }

    public function updateTrainer(Request $request, User $trainer): RedirectResponse
    {
        abort_unless((int) $trainer->user_type === 2, 404, 'Trainer not found.');

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($trainer->id)],
            'status' => ['required', 'boolean'],
        ]);

        $payload['status'] = (bool) $payload['status'];
        $payload['user_type'] = 2;

        $trainer->update($payload);

        return redirect()
            ->route('admin.gym.trainers')
            ->with('success', 'Trainer updated successfully.');
    }

    public function destroyTrainer(User $trainer): RedirectResponse
    {
        abort_unless((int) $trainer->user_type === 2, 404, 'Trainer not found.');

        $trainer->delete();

        return redirect()
            ->route('admin.gym.trainers')
            ->with('success', 'Trainer deleted successfully.');
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

    protected function enquiryColumns(): array
    {
        return Schema::hasTable('gym_enquiries')
            ? Schema::getColumnListing('gym_enquiries')
            : [];
    }

    protected function editableEnquiryColumns(): array
    {
        $blocked = [
            (new GymEnquiry())->getKeyName(),
            'id',
            'uuid',
            'user_id',
            'gym_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        return array_values(array_filter(
            $this->enquiryColumns(),
            fn (string $column) => ! in_array($column, $blocked, true)
        ));
    }

    protected function enquiryColumnTypes(array $columns): array
    {
        $types = [];

        foreach ($columns as $column) {
            $types[$column] = Schema::getColumnType('gym_enquiries', $column);
        }

        return $types;
    }

    protected function enquiryValidationRules(): array
    {
        $rules = [];

        foreach ($this->editableEnquiryColumns() as $column) {
            $type = Schema::getColumnType('gym_enquiries', $column);

            $rules[$column] = match ($type) {
                'boolean' => ['nullable', 'boolean'],
                'integer', 'bigint', 'smallint', 'tinyint', 'mediumint' => ['nullable', 'integer'],
                'decimal', 'float', 'double' => ['nullable', 'numeric'],
                'date', 'datetime', 'timestamp', 'time' => ['nullable', 'date'],
                'json' => ['nullable', 'json'],
                default => ['nullable', 'string'],
            };
        }

        return $rules;
    }

    protected function reviewSortColumn(array $columns): ?string
    {
        foreach (['rating', 'avg_rating', 'rating_avg', 'score'] as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        return null;
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
                    ['label' => 'Trainers', 'route' => 'admin.gym.trainers'],
                    ['label' => 'Attendance / Bookings', 'route' => 'admin.gym.attendance'],
                    ['label' => 'Gym Enquiries', 'route' => 'admin.gym.enquiry'],
                    ['label' => 'Gallery Media', 'route' => 'admin.gym.gallery-media'],
                    ['label' => 'Gym Reviews', 'route' => 'admin.gym.reviews'],
                ],
            ],
            [
                'label' => 'User Operations',
                'type' => 'dropdown',
                'icon' => 'modules',
                'items' => [
                    ['label' => 'Manage Users', 'route' => 'admin.dashboard'],
                    ['label' => 'User Operations API', 'route' => 'admin.dashboard'],
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

    protected function updateFeePlanApprovalFromRequest(Request $request, GymFeePlan $feePlan, string $targetState): RedirectResponse
    {
        $this->ensureSuperAdmin($request);

        $result = $this->updateFeePlanApproval($feePlan, $targetState);

        return redirect()
            ->route('admin.gym.fee-plans.view', $feePlan)
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    protected function updateFeePlanApproval(GymFeePlan $feePlan, string $targetState): array
    {
        $table = $feePlan->getTable();
        $approved = $targetState === 'approved';
        $payload = [];

        if (! Schema::hasTable($table)) {
            return [
                'success' => false,
                'message' => 'The fee plan table is not available right now.',
            ];
        }

        if (Schema::hasColumn($table, 'approval_status')) {
            $payload['approval_status'] = $targetState;
        } elseif (Schema::hasColumn($table, 'status')) {
            $currentStatus = strtolower(trim((string) $feePlan->getAttribute('status')));
            $payload['status'] = in_array($currentStatus, ['pending', 'active', 'rejected', 'suspended'], true)
                ? ($approved ? 'active' : 'rejected')
                : $targetState;
        } elseif (Schema::hasColumn($table, 'is_approved')) {
            $payload['is_approved'] = $approved;
        } elseif (Schema::hasColumn($table, 'approved')) {
            $payload['approved'] = $approved;
        } elseif (Schema::hasColumn($table, 'is_active')) {
            $payload['is_active'] = $approved;
        } else {
            return [
                'success' => false,
                'message' => 'No approval field was found on gym fee plans (expected one of approval_status/status/is_approved/approved/is_active).',
            ];
        }

        if (Schema::hasColumn($table, 'updated_at')) {
            $payload['updated_at'] = now();
        }

        $feePlan->forceFill($payload)->save();

        return [
            'success' => true,
            'message' => $approved
                ? 'Fee plan approved successfully.'
                : 'Fee plan disapproved successfully.',
        ];
    }

    protected function approvalState(Model $model): array
    {
        $attributes = $model->getAttributes();
        $rawState = null;

        if (array_key_exists('approval_status', $attributes)) {
            $rawState = strtolower(trim((string) $model->getAttribute('approval_status')));
        } elseif (array_key_exists('status', $attributes)) {
            $rawState = strtolower(trim((string) $model->getAttribute('status')));
        } elseif (array_key_exists('is_approved', $attributes)) {
            $rawState = $model->getAttribute('is_approved') ? 'approved' : 'disapproved';
        } elseif (array_key_exists('approved', $attributes)) {
            $rawState = $model->getAttribute('approved') ? 'approved' : 'disapproved';
        } elseif (array_key_exists('is_active', $attributes)) {
            $rawState = $model->getAttribute('is_active') ? 'approved' : 'disapproved';
        }

        $state = match ($rawState) {
            'approved', 'active', '1', 'true' => 'approved',
            'disapproved', 'rejected', 'suspended', 'inactive', 'disabled', '0', 'false' => 'disapproved',
            default => 'pending',
        };

        $label = match ($state) {
            'approved' => 'Approved',
            'disapproved' => 'Disapproved',
            default => 'Pending',
        };

        return [
            'state' => $state,
            'label' => $label,
        ];
    }

    protected function isSuperAdmin(Request $request): bool
    {
        $admin = $request->user('admin');

        if (! $admin) {
            return false;
        }

        if ((int) $admin->user_type === 0) {
            return true;
        }

        return $admin->hasAnyRole(['super-admin', 'super admin']);
    }

    protected function ensureSuperAdmin(Request $request): void
    {
        abort_unless($this->isSuperAdmin($request), 403, 'Only super admins can approve or disapprove fee plans.');
    }
}

