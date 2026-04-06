<header class="admin-topbar" role="banner">
    <div class="topbar-brand">
        <div class="brand-logo-chip brand-logo-chip-sm">
            <img class="brand-logo-image" src="{{ asset('images/fitpaxpro-logo.png') }}" alt="FitPaxPro logo">
        </div>
        <span>FitPaxPro</span>
    </div>

    <form class="topbar-search" role="search" onsubmit="event.preventDefault();">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
        <input type="search" name="dashboard_search" placeholder="Search gyms, plans, bookings..." aria-label="Global search">
    </form>

    <div class="topbar-right">
        <button class="icon-button mobile-menu-button" type="button" data-mobile-sidebar-toggle aria-label="Open sidebar">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
        </button>

        <button class="icon-button topbar-notify" type="button" aria-label="Notifications">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 17H9"></path><path d="M18 17V11a6 6 0 1 0-12 0v6l-2 2h16l-2-2Z"></path></svg>
            <span class="topbar-notify-dot" aria-hidden="true"></span>
        </button>

        <details class="profile-dropdown">
            <summary class="profile-toggle" aria-label="Profile menu">
                <div class="avatar">{{ $adminInitials ?? 'SA' }}</div>
                <div class="profile-text">
                    <strong>{{ $admin->name }}</strong>
                    <div class="muted">{{ $admin->email }}</div>
                </div>
                <svg class="sidebar-caret" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
            </summary>
            <div class="profile-menu">
                <div class="profile-menu-head">
                    <div class="avatar">{{ $adminInitials ?? 'SA' }}</div>
                    <div class="profile-text">
                        <strong>{{ $admin->name }}</strong>
                        <div class="muted">{{ $admin->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">Logout</button>
                </form>
            </div>
        </details>
    </div>
</header>
