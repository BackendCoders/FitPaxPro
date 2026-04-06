<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FitPaxPro Admin')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --brand-ink: #0b0b0c;
            --brand-slate: #202228;
            --brand-muted: #6b7280;
            --brand-line: rgba(17, 24, 39, 0.14);
            --brand-surface: rgba(255, 255, 255, 0.92);
            --brand-primary: #111827;
            --brand-primary-deep: #030712;
            --brand-accent: #4b5563;
            --brand-radius: 24px;
            --brand-shadow: 0 18px 44px rgba(15, 23, 42, 0.12);
            --brand-shadow-soft: 0 8px 24px rgba(15, 23, 42, 0.08);
            --sidebar-width: 282px;
            --sidebar-collapsed-width: 90px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            color: var(--brand-ink);
            background:
                radial-gradient(circle at top left, rgba(17, 24, 39, 0.1), transparent 30%),
                radial-gradient(circle at top right, rgba(55, 65, 81, 0.08), transparent 26%),
                linear-gradient(180deg, #f7f7f8 0%, #eef0f3 52%, #f7f7f8 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        .admin-app {
            min-height: 100vh;
            padding: 18px;
        }

        .admin-container {
            max-width: 1520px;
            margin: 0 auto;
        }

        .admin-shell {
            display: grid;
            grid-template-columns: var(--sidebar-width) minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .admin-shell.sidebar-collapsed {
            grid-template-columns: var(--sidebar-collapsed-width) minmax(0, 1fr);
        }

        .admin-sidebar,
        .admin-topbar,
        .surface-card,
        .metric-card,
        .chat-card,
        .panel-card,
        .record-card {
            border: 1px solid var(--brand-line);
            background: var(--brand-surface);
            box-shadow: var(--brand-shadow);
            backdrop-filter: blur(18px);
        }

        .admin-sidebar {
            position: sticky;
            top: 18px;
            height: calc(100vh - 36px);
            border-radius: 24px;
            padding: 16px 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
            transition: padding 0.2s ease;
        }

        .brand-lockup {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 8px 8px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #111827, #000000);
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.05em;
            flex-shrink: 0;
        }

        .brand-logo-chip {
            width: 76px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid rgba(17, 24, 39, 0.1);
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 8px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .brand-logo-chip-sm {
            width: 66px;
            height: 38px;
            border-radius: 10px;
        }

        .brand-logo-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .brand-copy strong,
        .account-copy strong {
            display: block;
            font-size: 1rem;
            line-height: 1.2;
        }

        .muted {
            color: var(--brand-muted);
        }

        .sidebar-toolbar {
            display: flex;
            justify-content: flex-end;
            padding: 0 6px;
        }

        .icon-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid var(--brand-line);
            background: rgba(255, 255, 255, 0.84);
            color: var(--brand-slate);
            cursor: pointer;
        }

        .icon-button svg {
            width: 18px;
            height: 18px;
        }

        .mobile-menu-button {
            display: none;
        }

        .sidebar-nav {
            display: grid;
            gap: 8px;
        }

        .sidebar-link,
        .sidebar-sublink,
        .sidebar-dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 14px;
            padding: 11px 12px;
            color: var(--brand-slate);
            font-weight: 600;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sidebar-dropdown-toggle {
            width: 100%;
            border: 0;
            background: transparent;
            text-align: left;
            cursor: pointer;
        }

        .sidebar-link:hover,
        .sidebar-sublink:hover,
        .sidebar-dropdown-toggle:hover {
            background: rgba(17, 24, 39, 0.08);
            color: #000;
        }

        .sidebar-link.active,
        .sidebar-sublink.active,
        .sidebar-dropdown-toggle.active {
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-primary-deep));
            color: #fff;
        }

        .sidebar-icon {
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-icon svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sidebar-caret {
            margin-left: auto;
            width: 16px;
            height: 16px;
            transition: transform 0.2s ease;
        }

        .sidebar-dropdown {
            display: grid;
            gap: 8px;
        }

        .sidebar-dropdown[open] .sidebar-caret {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.2s ease;
            margin-left: 10px;
        }

        .sidebar-dropdown[open] .sidebar-submenu {
            grid-template-rows: 1fr;
        }

        .sidebar-submenu-inner {
            overflow: hidden;
            display: grid;
            gap: 6px;
            padding-left: 14px;
            border-left: 1px dashed rgba(148, 163, 184, 0.32);
        }

        .sidebar-footnote {
            margin-top: auto;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            padding: 12px;
            background: rgba(255, 255, 255, 0.64);
            font-size: 0.84rem;
            color: var(--brand-muted);
        }

        .admin-workspace {
            min-width: 0;
            display: grid;
            gap: 16px;
        }

        .admin-topbar {
            position: sticky;
            top: 18px;
            z-index: 20;
            border-radius: 22px;
            padding: 12px 16px;
            display: grid;
            grid-template-columns: auto minmax(240px, 520px) auto;
            align-items: center;
            gap: 14px;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }

        .topbar-search {
            position: relative;
        }

        .topbar-search svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            stroke: #64748b;
            fill: none;
            stroke-width: 2;
            pointer-events: none;
        }

        .topbar-search input {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, 0.26);
            border-radius: 999px;
            padding: 10px 14px 10px 38px;
            background: #fff;
            color: var(--brand-slate);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-self: end;
        }

        .topbar-notify {
            position: relative;
        }

        .topbar-notify-dot {
            position: absolute;
            right: 9px;
            top: 9px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #111827;
            border: 2px solid #fff;
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-toggle {
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: rgba(255, 255, 255, 0.88);
            border-radius: 14px;
            padding: 7px 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            min-width: 220px;
            list-style: none;
        }

        .profile-toggle::-webkit-details-marker {
            display: none;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.12), rgba(75, 85, 99, 0.18));
            color: #111827;
            font-weight: 700;
            flex-shrink: 0;
        }

        .profile-text {
            min-width: 0;
        }

        .profile-text strong {
            display: block;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-text .muted {
            font-size: 0.82rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            width: 260px;
            border-radius: 16px;
            border: 1px solid var(--brand-line);
            background: #fff;
            box-shadow: var(--brand-shadow-soft);
            padding: 12px;
            display: none;
            z-index: 22;
        }

        .profile-dropdown[open] .profile-menu {
            display: block;
        }

        .profile-menu-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
            margin-bottom: 10px;
        }

        .logout-button {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #111827, #030712);
        }

        .admin-content {
            min-width: 0;
        }

        .surface-card,
        .panel-card {
            border-radius: var(--brand-radius);
            padding: 24px;
        }

        .page-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(280px, 0.85fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        .hero-card {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(17, 24, 39, 0.96), rgba(2, 6, 23, 0.95)),
                linear-gradient(135deg, rgba(75, 85, 99, 0.24), transparent);
            color: #f8fafc;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            right: -42px;
            bottom: -62px;
            width: 210px;
            height: 210px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .hero-kicker,
        .mini-chip {
            width: fit-content;
            padding: 8px 12px;
            font-size: 0.82rem;
            border-radius: 999px;
        }

        .hero-kicker {
            background: rgba(255, 255, 255, 0.12);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-title {
            margin: 16px 0 10px;
            font-size: clamp(2rem, 3vw, 3.25rem);
            line-height: 1;
        }

        .hero-actions,
        .badge-row,
        .record-chip-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ghost-button,
        .primary-button,
        .mini-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 999px;
            font-weight: 600;
        }

        .primary-button,
        .ghost-button {
            padding: 12px 16px;
        }

        .primary-button {
            border: 0;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-primary-deep));
            color: #fff;
        }

        .ghost-button {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
        }

        .side-stack {
            display: grid;
            gap: 18px;
        }

        .mini-chip {
            border: 1px solid rgba(17, 24, 39, 0.14);
            background: rgba(17, 24, 39, 0.08);
            color: #111827;
        }

        .metrics-grid,
        .record-grid {
            display: grid;
            gap: 18px;
        }

        .metrics-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 22px;
        }

        .metric-card,
        .chat-card,
        .record-card {
            border-radius: 24px;
            padding: 20px;
        }

        .metric-value {
            margin: 14px 0 6px;
            font-size: 2rem;
            font-weight: 800;
        }

        .metric-card.teal {
            border-top: 4px solid #111827;
        }

        .metric-card.orange {
            border-top: 4px solid #374151;
        }

        .metric-card.amber {
            border-top: 4px solid #4b5563;
        }

        .metric-card.slate {
            border-top: 4px solid #6b7280;
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            gap: 20px;
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 1.35rem;
        }

        .chat-list,
        .insight-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .chat-item {
            padding: 16px;
            border-radius: 20px;
            background: rgba(248, 250, 252, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        .chart-wrap {
            display: grid;
            grid-template-columns: 180px minmax(0, 1fr);
            gap: 18px;
            align-items: center;
            margin-top: 18px;
        }

        .chart-ring {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            position: relative;
            margin: 0 auto;
        }

        .chart-ring::after {
            content: "";
            position: absolute;
            inset: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.14);
        }

        .chart-label {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            z-index: 1;
            font-weight: 700;
            text-align: center;
        }

        .legend-item,
        .record-chip {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .record-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .record-chip {
            padding: 8px 10px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid rgba(148, 163, 184, 0.18);
            font-size: 0.86rem;
        }

        .admin-shell.sidebar-collapsed .brand-copy,
        .admin-shell.sidebar-collapsed .sidebar-label-text,
        .admin-shell.sidebar-collapsed .sidebar-caret,
        .admin-shell.sidebar-collapsed .sidebar-submenu,
        .admin-shell.sidebar-collapsed .sidebar-footnote {
            display: none;
        }

        .admin-shell.sidebar-collapsed .sidebar-link,
        .admin-shell.sidebar-collapsed .sidebar-dropdown-toggle {
            justify-content: center;
        }

        .admin-shell.sidebar-collapsed .sidebar-collapse-button svg {
            transform: rotate(180deg);
        }

        .layout-backdrop {
            display: none;
        }

        @media (max-width: 1180px) {
            .page-hero,
            .content-grid {
                grid-template-columns: 1fr;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 980px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: fixed;
                top: 18px;
                left: 18px;
                width: min(84vw, 320px);
                height: calc(100vh - 36px);
                transform: translateX(-115%);
                opacity: 0;
                transition: transform 0.22s ease, opacity 0.22s ease;
                z-index: 40;
            }

            body.sidebar-open .admin-sidebar {
                transform: translateX(0);
                opacity: 1;
            }

            .layout-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 32;
            }

            body.sidebar-open .layout-backdrop {
                display: block;
            }

            .sidebar-collapse-button {
                display: none;
            }

            .admin-topbar {
                top: 14px;
                grid-template-columns: minmax(0, 1fr);
            }

            .topbar-brand {
                display: none;
            }

            .topbar-right {
                justify-self: stretch;
                justify-content: space-between;
            }

            .profile-toggle {
                min-width: 0;
                width: 100%;
            }

            .mobile-menu-button {
                display: inline-flex;
            }
        }

        @media (max-width: 760px) {
            .admin-app {
                padding: 12px;
            }

            .metrics-grid,
            .chart-wrap {
                grid-template-columns: 1fr;
            }

            .panel-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-right {
                flex-wrap: wrap;
            }

            .profile-text .muted {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-app">
        <div class="admin-container">
            @yield('content')
        </div>
    </div>

    <div class="layout-backdrop" data-mobile-sidebar-close></div>

    <script>
        (function () {
            function getShell() {
                return document.querySelector('.admin-shell');
            }

            function setDesktopCollapseState() {
                var shell = getShell();
                if (!shell || window.innerWidth <= 980) {
                    return;
                }

                if (localStorage.getItem('fitpaxpro_admin_sidebar_collapsed') === '1') {
                    shell.classList.add('sidebar-collapsed');
                }
            }

            function closeMobileSidebar() {
                document.body.classList.remove('sidebar-open');
            }

            document.addEventListener('DOMContentLoaded', setDesktopCollapseState);

            document.addEventListener('click', function (event) {
                var shell = getShell();
                if (!shell) {
                    return;
                }

                if (event.target.closest('[data-sidebar-toggle]')) {
                    shell.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('fitpaxpro_admin_sidebar_collapsed', shell.classList.contains('sidebar-collapsed') ? '1' : '0');
                }

                if (event.target.closest('[data-mobile-sidebar-toggle]')) {
                    document.body.classList.toggle('sidebar-open');
                }

                if (event.target.closest('[data-mobile-sidebar-close]')) {
                    closeMobileSidebar();
                }

                if (event.target.closest('.sidebar-link') || event.target.closest('.sidebar-sublink')) {
                    closeMobileSidebar();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMobileSidebar();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 980) {
                    closeMobileSidebar();
                }
            });
        })();
    </script>
</body>
</html>
