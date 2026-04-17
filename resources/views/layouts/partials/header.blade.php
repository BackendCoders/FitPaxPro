<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button type="button" class="button-toggle-menu nav-link btnlefsidedesk">
                        <iconify-icon icon="tabler:align-left"
                            class="fs-20 align-middle topbar-button"></iconify-icon>
                    </button>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                @auth
                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <iconify-icon icon="tabler:bell"
                                class="fs-20 align-middle topbar-button"></iconify-icon>

                            <span
                                class="badge bg-danger rounded-circle noti-icon-badge {{ auth()->user()->unreadNotifications->count() > 0 ? '' : 'd-none' }}"
                                id="notification-badge">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end dropdown-xl">
                            <div class="dropdown-item noti-title">
                                <h5 class="m-0 fs-16">
                                    <span class="float-end">
                                        <a href="{{ Route::has('notifications.markAsRead') ? route('notifications.markAsRead') : '#' }}"
                                            class="text-light">
                                            <small><iconify-icon icon="tabler:checks"
                                                    class="fs-18 align-middle"></iconify-icon> Mark all
                                                read</small>
                                        </a>
                                    </span>
                                    Notifications
                                </h5>
                            </div>

                            <div class="noti-scroll" data-simplebar id="notification-list">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <a href="#" class="dropdown-item notify-item">
                                        <div class="d-flex align-items-start">
                                            <div
                                                class="notify-icon bg-{{ $notification->data['type'] ?? 'primary' }}-subtle text-{{ $notification->data['type'] ?? 'primary' }}">
                                                <iconify-icon icon="{{ $notification->data['icon'] ?? 'tabler:info-circle' }}"
                                                    class="fs-18"></iconify-icon>
                                            </div>
                                            <div class="notify-details ms-2">
                                                <h6 class="notify-title mb-1 fw-semibold">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </h6>
                                                <p class="notify-desc mb-0 text-muted fs-12">
                                                    {{ $notification->data['message'] ?? '' }}
                                                </p>
                                                <p class="notify-time mb-0 text-muted fs-11">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="dropdown-item text-center p-3 text-muted">
                                        <small>No new notifications</small>
                                    </div>
                                @endforelse
                            </div>

                            <a href="javascript:void(0);" id="view-all-notifications"
                                class="dropdown-item text-center text-light notify-item notify-all bg-dark">
                                View all <i data-lucide="arrow-right"></i>
                            </a>
                        </div>
                    </li>

                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}"
                                alt="user" class="rounded-circle" style="object-fit: cover; width: 32px; height: 32px;" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0">Welcome {{ auth()->user()->name }}!</h6>
                            </div>
                            <a href="#" class="dropdown-item notify-item">
                                <iconify-icon icon="tabler:user-square-rounded" class="fs-18 align-middle"></iconify-icon>
                                <span>My Account</span>
                            </a>
                            <div class="dropdown-divider"></div>

                            <a href="javascript:void(0);" class="dropdown-item notify-item"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <iconify-icon icon="tabler:logout" class="fs-18 align-middle"></iconify-icon>
                                <span>Logout</span>
                            </a>
                        </div>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationsModalLabel">All Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="all-notifications-list">
                <div class="text-center text-muted py-4">Loading notifications...</div>
            </div>
        </div>
    </div>
</div>