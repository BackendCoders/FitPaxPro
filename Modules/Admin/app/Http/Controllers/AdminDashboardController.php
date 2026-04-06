<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Blog;
use App\Models\Gym;
use App\Models\GymEnquiry;
use App\Models\GymFeePlan;
use App\Models\GymGalleryMedia;
use App\Models\GymReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('admin::dashboard.index', $this->buildBaseViewData($request) + [
            'dashboard' => $this->buildDashboardData(),
        ]);
    }

    public function createGym(Request $request)
    {
        return view('admin::gym.create', $this->buildBaseViewData($request) + [
            'page' => [
                'eyebrow' => 'Gym Operations',
                'title' => 'Create Gym',
                'description' => 'A dedicated route for onboarding new gyms with owner assignment, visibility controls, and launch-readiness checkpoints.',
                'stats' => [
                    ['label' => 'Existing gyms', 'value' => $this->safeCount(new Gym())],
                    ['label' => 'Published plans', 'value' => $this->safeCount(new GymFeePlan())],
                    ['label' => 'Gallery assets', 'value' => $this->safeCount(new GymGalleryMedia())],
                ],
                'highlights' => [
                    'Set up a gym record with owner context and operational metadata.',
                    'Review fee plans and gallery readiness before publishing.',
                    'Keep create, edit, and delete flows isolated for cleaner admin scaling.',
                ],
            ],
        ]);
    }

    public function editGym(Request $request)
    {
        return view('admin::gym.edit', $this->buildBaseViewData($request) + [
            'page' => [
                'eyebrow' => 'Gym Operations',
                'title' => 'Edit Gym',
                'description' => 'A focused management surface for updating gym details, ownership, verification, and public-facing content without mixing in unrelated modules.',
                'stats' => [
                    ['label' => 'Verified gyms', 'value' => $this->safeCountWhere(new Gym(), ['is_verified' => 1])],
                    ['label' => 'Pending enquiries', 'value' => $this->safeNullCount(new GymEnquiry(), 'responded_at')],
                    ['label' => 'Reviews logged', 'value' => $this->safeCount(new GymReview())],
                ],
                'highlights' => [
                    'Adjust gym profile data from its own dedicated route.',
                    'Track records that may need admin attention before editing.',
                    'Preserve modularity by keeping update workflows separated from overview screens.',
                ],
            ],
        ]);
    }

    public function deleteGym(Request $request)
    {
        return view('admin::gym.delete', $this->buildBaseViewData($request) + [
            'page' => [
                'eyebrow' => 'Gym Operations',
                'title' => 'Delete Gym',
                'description' => 'A dedicated route for controlled gym deactivation or removal, with nearby operational metrics visible before destructive decisions are taken.',
                'stats' => [
                    ['label' => 'Gyms in system', 'value' => $this->safeCount(new Gym())],
                    ['label' => 'Attendance logs', 'value' => $this->safeCount(new AttendanceLog())],
                    ['label' => 'Related media items', 'value' => $this->safeCount(new GymGalleryMedia())],
                ],
                'highlights' => [
                    'Review downstream dependencies before a delete action is initiated.',
                    'Keep sensitive removal actions isolated from browsing and editing flows.',
                    'Support safer admin operations as the platform grows.',
                ],
            ],
        ]);
    }

    public function gymDirectory(Request $request)
    {
        return view('admin::gym.index', $this->buildBaseViewData($request) + [
            'modulePage' => $this->buildModulePageData(
                'Gym',
                'Gym locations, owners, verification state, and base platform records.',
                new Gym(),
                ['Active route' => route('admin.gym.index'), 'Supports' => 'Create, edit, delete'],
                ['Total gyms' => $this->safeCount(new Gym()), 'Verified gyms' => $this->safeCountWhere(new Gym(), ['is_verified' => 1])]
            ),
        ]);
    }

    public function attendance(Request $request)
    {
        return view('admin::gym.attendance', $this->buildBaseViewData($request) + [
            'modulePage' => $this->buildModulePageData(
                'Attendance Log',
                'Daily gym check-ins and verification activity coming from the existing attendance model.',
                new AttendanceLog(),
                ['Route' => route('admin.gym.attendance'), 'Linked domain' => 'Gym operations'],
                ['Attendance logs' => $this->safeCount(new AttendanceLog()), 'Verified check-ins' => $this->safeCountWhere(new AttendanceLog(), ['is_verified' => 1])]
            ),
        ]);
    }

    public function enquiry(Request $request)
    {
        return view('admin::gym.enquiry', $this->buildBaseViewData($request) + [
            'modulePage' => $this->buildModulePageData(
                'Gym Enquiry',
                'Lead and interest tracking from users who have reached out to gyms through the platform.',
                new GymEnquiry(),
                ['Route' => route('admin.gym.enquiry'), 'Use case' => 'Lead follow-up'],
                ['Total enquiries' => $this->safeCount(new GymEnquiry()), 'Awaiting response' => $this->safeNullCount(new GymEnquiry(), 'responded_at')]
            ),
        ]);
    }

    public function feePlans(Request $request)
    {
        return view('admin::gym.fee-plans', $this->buildBaseViewData($request) + [
            'modulePage' => $this->buildModulePageData(
                'Gym Fee Plan',
                'Pricing, duration, trainer availability, and plan activation details for gym memberships.',
                new GymFeePlan(),
                ['Route' => route('admin.gym.fee-plans'), 'Focus' => 'Commercial configuration'],
                ['Fee plans' => $this->safeCount(new GymFeePlan()), 'Active plans' => $this->safeCountWhere(new GymFeePlan(), ['is_active' => 1])]
            ),
        ]);
    }

    public function galleryMedia(Request $request)
    {
        return view('admin::gym.gallery-media', $this->buildBaseViewData($request) + [
            'modulePage' => $this->buildModulePageData(
                'Gym Gallery Media',
                'Images and video assets that shape each gym listing and public media presentation.',
                new GymGalleryMedia(),
                ['Route' => route('admin.gym.gallery-media'), 'Focus' => 'Content operations'],
                ['Media assets' => $this->safeCount(new GymGalleryMedia()), 'Main videos' => $this->safeCountWhere(new GymGalleryMedia(), ['is_main_video' => 1])]
            ),
        ]);
    }

    public function reviews(Request $request)
    {
        return view('admin::gym.reviews', $this->buildBaseViewData($request) + [
            'modulePage' => $this->buildModulePageData(
                'Gym Review',
                'Ratings, featured feedback, and response activity captured from existing gym review records.',
                new GymReview(),
                ['Route' => route('admin.gym.reviews'), 'Focus' => 'Trust and feedback'],
                ['Reviews' => $this->safeCount(new GymReview()), 'Featured reviews' => $this->safeCountWhere(new GymReview(), ['is_featured' => 1])]
            ),
        ]);
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
                'route' => 'admin.gym.edit',
                'type' => 'link',
                'icon' => 'settings',
            ],
        ];
    }

    protected function buildDashboardData(): array
    {
        $summary = [
            ['label' => 'Total Users', 'value' => $this->safeCount(new User()), 'accent' => 'teal'],
            ['label' => 'Total Gyms', 'value' => $this->safeCount(new Gym()), 'accent' => 'orange'],
            ['label' => 'Total Trainers', 'value' => $this->safeCountWhere(new User(), ['user_type' => 2]), 'accent' => 'amber'],
            ['label' => 'Total Blogs', 'value' => $this->safeCount(new Blog()), 'accent' => 'slate'],
        ];

        $analytics = collect($summary)->map(fn (array $item) => [
            'label' => $item['label'],
            'value' => $item['value'],
        ])->all();

        $total = max(1, array_sum(array_column($analytics, 'value')));
        $chartStops = [];
        $legend = [];
        $colors = ['#0f766e', '#f97316', '#d97706', '#334155'];
        $offset = 0;

        foreach ($analytics as $index => $item) {
            $share = round(($item['value'] / $total) * 100, 2);
            $color = $colors[$index] ?? '#94a3b8';
            $chartStops[] = sprintf('%s %s%% %s%%', $color, $offset, min(100, $offset + $share));
            $legend[] = $item + ['color' => $color, 'share' => $share];
            $offset += $share;
        }

        return [
            'summary' => $summary,
            'chatItems' => $this->buildChatItems(),
            'analytics' => [
                'chart' => implode(', ', $chartStops),
                'legend' => $legend,
            ],
        ];
    }

    protected function buildChatItems(): array
    {
        $items = collect();

        if ($this->tableExists(new GymEnquiry())) {
            $items = $items->merge(
                GymEnquiry::query()
                    ->with(['gym', 'user'])
                    ->latest()
                    ->take(4)
                    ->get()
                    ->map(function (GymEnquiry $enquiry): array {
                        return [
                            'title' => optional($enquiry->user)->name ?: 'Platform user',
                            'context' => optional($enquiry->gym)->name ?: 'Gym enquiry',
                            'message' => $this->truncateText((string) ($enquiry->message ?? $enquiry->subject ?? 'New gym enquiry received.')),
                            'meta' => $enquiry->created_at?->format('M d, Y h:i A') ?: 'Recent',
                            'timestamp' => $enquiry->created_at?->timestamp ?? 0,
                        ];
                    })
            );
        }

        if ($this->tableExists(new GymReview())) {
            $items = $items->merge(
                GymReview::query()
                    ->with(['gym', 'user'])
                    ->latest()
                    ->take(4)
                    ->get()
                    ->map(function (GymReview $review): array {
                        return [
                            'title' => optional($review->user)->name ?: 'Member feedback',
                            'context' => optional($review->gym)->name ?: 'Gym review',
                            'message' => $this->truncateText((string) ($review->review ?? $review->comment ?? 'A new gym review was posted.')),
                            'meta' => $review->created_at?->format('M d, Y h:i A') ?: 'Recent',
                            'timestamp' => $review->created_at?->timestamp ?? 0,
                        ];
                    })
            );
        }

        return $items
            ->sortByDesc('timestamp')
            ->take(6)
            ->values()
            ->map(function (array $item) {
                unset($item['timestamp']);

                return $item;
            })
            ->all();
    }

    protected function buildModulePageData(
        string $title,
        string $description,
        Model $model,
        array $badges = [],
        array $metrics = []
    ): array {
        $records = [];

        if ($this->tableExists($model)) {
            $records = $model->newQuery()
                ->latest()
                ->take(8)
                ->get()
                ->map(fn (Model $item) => $this->normalizeRecord($item))
                ->all();
        }

        return [
            'title' => $title,
            'description' => $description,
            'badges' => $badges,
            'metrics' => $metrics,
            'records' => $records,
        ];
    }

    protected function normalizeRecord(Model $model): array
    {
        $attributes = collect($model->getAttributes())
            ->reject(fn ($value, string $key) => in_array($key, ['updated_at', 'deleted_at'], true) || $value === null || $value === '')
            ->map(function ($value) {
                if (is_bool($value)) {
                    return $value ? 'Yes' : 'No';
                }

                return (string) $value;
            });

        $title = $attributes->first(fn ($value, string $key) => in_array($key, ['name', 'title', 'email', 'plan_name'], true))
            ?? class_basename($model).' #'.$model->getKey();

        $contextKey = $attributes->keys()->first(fn (string $key) => in_array($key, ['created_at', 'status', 'phone', 'city', 'price', 'rating', 'check_in_time'], true));
        $chips = $attributes
            ->reject(fn ($value, string $key) => in_array($key, ['name', 'title', 'email', 'plan_name'], true))
            ->take(4)
            ->map(fn ($value, string $key) => [
                'label' => str_replace('_', ' ', ucfirst($key)),
                'value' => $this->truncateText($value, 28),
            ])
            ->values()
            ->all();

        return [
            'title' => $this->truncateText($title, 48),
            'context' => $contextKey ? ucfirst(str_replace('_', ' ', $contextKey)).': '.$this->truncateText((string) $attributes->get($contextKey), 36) : 'Record ID: '.$model->getKey(),
            'chips' => $chips,
        ];
    }

    protected function safeCount(Model $model): int
    {
        if (! $this->tableExists($model)) {
            return 0;
        }

        return $model->newQuery()->count();
    }

    protected function safeCountWhere(Model $model, array $conditions): int
    {
        if (! $this->tableExists($model)) {
            return 0;
        }

        $query = $model->newQuery();

        foreach ($conditions as $column => $value) {
            if (Schema::hasColumn($model->getTable(), $column)) {
                $query->where($column, $value);
            }
        }

        return $query->count();
    }

    protected function safeNullCount(Model $model, string $column): int
    {
        if (! $this->tableExists($model) || ! Schema::hasColumn($model->getTable(), $column)) {
            return 0;
        }

        return $model->newQuery()->whereNull($column)->count();
    }

    protected function tableExists(Model $model): bool
    {
        return Schema::hasTable($model->getTable());
    }

    protected function truncateText(string $value, int $limit = 72): string
    {
        return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit - 3).'...' : $value;
    }
}
