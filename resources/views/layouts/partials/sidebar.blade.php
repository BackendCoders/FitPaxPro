@inject('settings', 'App\Services\SettingService')

<style>
    .app-sidebar-menu {
        background: #0f1115 !important;
        border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.5);
    }

    #sidebar-menu {
        padding: 20px 0;
    }

    .logo-box {
        padding: 30px 24px;
        margin-bottom: 20px;
    }

    .menu-title {
        padding: 12px 24px !important;
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 1.5px !important;
        color: rgba(255, 255, 255, 0.3) !important;
    }

    .tp-link {
        display: flex !important;
        align-items: center !important;
        padding: 12px 24px !important;
        color: rgba(255, 255, 255, 0.6) !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border-left: 3px solid transparent !important;
    }

    .tp-link:hover {
        color: #fff !important;
        background: rgba(225, 18, 24, 0.05) !important;
    }

    .tp-link.active {
        color: #fff !important;
        background: rgba(225, 18, 24, 0.08) !important;
        border-left-color: #E11218 !important;
    }

    .tp-link iconify-icon {
        font-size: 1.25rem !important;
        margin-right: 14px !important;
        transition: transform 0.3s !important;
    }

    .tp-link.active iconify-icon {
        color: #E11218 !important;
    }

    .tp-link:hover iconify-icon {
        transform: translateX(3px);
    }

    .nav-second-level {
        background: rgba(0, 0, 0, 0.2);
        padding-left: 20px;
    }

    .nav-second-level li a {
        padding: 10px 24px !important;
        font-size: 0.8rem !important;
        color: rgba(255, 255, 255, 0.4) !important;
    }

    .nav-second-level li a:hover,
    .nav-second-level li a.active {
        color: #fff !important;
    }

    .menu-arrow {
        transition: transform 0.3s;
    }

    .tp-link[aria-expanded="true"] .menu-arrow {
        transform: rotate(90deg);
    }

    /* Force visibility */
    .logo-box {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .logo-lg img {
        display: block !important;
    }
</style>

<div class="app-sidebar-menu">
    <!-- Clean Logo Header -->
    <div class="logo-box text-center py-4 border-bottom border-secondary border-opacity-10"
        style="background: rgba(15, 17, 21, 0.95); backdrop-filter: blur(10px);">
        <a href="{{ route('admin.dashboard') }}" class="logo-content">
            <img src="{{ $settings->getImageUrl('logo') }}" alt="FitPaxPro" height="30"
                style="filter: drop-shadow(0 0 10px rgba(225,18,24,0.3));">
        </a>
    </div>

    <div class="h-100" data-simplebar>
        <div id="sidebar-menu" class="pt-2">

            <ul id="sidebar-menu">

                <li class="menu-title">Main Workspace</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="tp-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:smart-home"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Dashboard </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.platform-plans.index') }}"
                        class="tp-link {{ request()->routeIs('admin.platform-plans.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:crown"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Master Tiers </span>
                    </a>
                </li>

                <li class="menu-title">Infrastructure</li>

                <li>
                    <a href="{{ route('users.index') }}"
                        class="tp-link {{ request()->is('users*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:users"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Operative Directory </span>
                    </a>
                </li>

                <li>
                    <a href="#sidebarGym" data-bs-toggle="collapse"
                        class="tp-link {{ request()->is('gym*') && !request()->routeIs('gym.plans.*') && !request()->routeIs('gym.subscriptions.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:building-community"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Gym Management </span>
                        <span class="menu-arrow d-none"></span>
                    </a>
                    <div class="collapse {{ request()->is('gym*') && !request()->routeIs('gym.plans.*') && !request()->routeIs('gym.subscriptions.*') ? 'show' : '' }}"
                        id="sidebarGym">
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="{{ route('gym.index') }}"
                                    class="{{ request()->routeIs('gym.index') ? 'active' : '' }}">Directory</a>
                            </li>
                            <li>
                                <a href="{{ route('gym.create') }}"
                                    class="{{ request()->routeIs('gym.create') ? 'active' : '' }}">Launch Location</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ route('gym.plans.index') }}"
                        class="tp-link {{ request()->routeIs('gym.plans.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:list-check"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Membership Plans </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('gym.subscriptions.index') }}"
                        class="tp-link {{ request()->routeIs('gym.subscriptions.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:users-group"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Subscriber Hub </span>
                    </a>
                </li>

                <li class="menu-title">Control Panel</li>

                <li>
                    <a href="{{ route('admin.custom-fields.index') }}"
                        class="tp-link {{ request()->routeIs('admin.custom-fields.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:database-cog"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Dynamic Fields </span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.banners.index') }}"
                        class="tp-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:photo-up"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> App Banners </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.categories.index') }}"
                        class="tp-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:category-2"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> System Categories </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.settings.index') }}"
                        class="tp-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <span class="nav-icon text-white">
                            <iconify-icon icon="tabler:settings-2"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> System Config </span>
                    </a>
                </li>

                <li class="mt-4">
                    <a href="javascript:void(0);" class="tp-link text-danger"
                        style="border-left-color: rgba(225,18,24,0.3) !important;"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <span class="nav-icon">
                            <iconify-icon icon="tabler:logout" class="text-danger"></iconify-icon>
                        </span>
                        <span class="sidebar-text"> Terminate Session </span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>