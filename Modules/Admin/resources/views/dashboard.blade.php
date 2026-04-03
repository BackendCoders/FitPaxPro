@extends('admin::components.layouts.master')

@section('title', 'FitPaxPro Super Admin Dashboard')

@push('styles')
    <style>
        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
        }

        .admin-sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 28px 22px;
            background: rgba(15, 23, 42, 0.94);
            color: #e2e8f0;
            border-right: 1px solid rgba(148, 163, 184, 0.15);
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.04em;
            box-shadow: 0 14px 28px rgba(15, 118, 110, 0.35);
        }

        .sidebar-group {
            margin-top: 28px;
        }

        .sidebar-label {
            margin: 0 0 12px;
            color: #94a3b8;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            color: #e2e8f0;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .sidebar-link:hover {
            background: rgba(148, 163, 184, 0.13);
            transform: translateX(3px);
        }

        .sidebar-badge,
        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 600;
        }

        .sidebar-badge {
            background: rgba(148, 163, 184, 0.16);
            color: #cbd5e1;
        }

        .admin-main {
            padding: 28px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 24px;
            padding: 20px 24px;
            border: 1px solid var(--brand-line);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(16px);
            box-shadow: var(--brand-shadow);
        }

        .page-title {
            margin: 0;
            font-size: clamp(2rem, 2.8vw, 3rem);
            line-height: 1;
        }

        .page-subtitle,
        .muted {
            margin: 0;
            color: var(--brand-muted);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .chip,
        .account-card,
        .stat-card,
        .hero-card,
        .module-card,
        .model-group {
            border: 1px solid var(--brand-line);
            background: var(--brand-surface);
            backdrop-filter: blur(16px);
            box-shadow: var(--brand-shadow);
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            color: var(--brand-slate);
        }

        .account-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 12px;
            border-radius: 18px;
        }

        .avatar {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.14), rgba(249, 115, 22, 0.24));
            color: var(--brand-primary-deep);
            font-size: 1.1rem;
            font-weight: 700;
        }

        .logout-button,
        .action-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 14px;
            border: 0;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .logout-button:hover,
        .action-link:hover {
            transform: translateY(-1px);
        }

        .logout-button {
            padding: 12px 16px;
            background: #0f172a;
            color: #fff;
        }

        .action-link {
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-primary-deep));
            color: #fff;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.9fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .hero-card,
        .module-card,
        .model-group,
        .stat-card {
            border-radius: 28px;
        }

        .hero-card {
            padding: 28px;
            background:
                linear-gradient(135deg, rgba(15, 118, 110, 0.96), rgba(15, 23, 42, 0.94)),
                linear-gradient(135deg, rgba(249, 115, 22, 0.22), transparent);
            color: #f8fafc;
            overflow: hidden;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: auto -80px -80px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .hero-card {
            position: relative;
        }

        .hero-kicker {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: rgba(248, 250, 252, 0.92);
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-title {
            margin: 18px 0 10px;
            font-size: clamp(2rem, 3vw, 3.25rem);
            line-height: 1;
        }

        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .hero-meta .pill {
            background: rgba(255, 255, 255, 0.12);
            color: #f8fafc;
        }

        .stats-grid,
        .modules-grid,
        .models-grid {
            display: grid;
            gap: 18px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .stat-card {
            padding: 22px;
        }

        .stat-value {
            margin: 14px 0 4px;
            font-size: 2rem;
            font-weight: 700;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            margin: 30px 0 16px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 1.4rem;
        }

        .modules-grid {
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .module-card,
        .model-group {
            padding: 24px;
        }

        .module-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .module-title,
        .model-title {
            margin: 0 0 8px;
            font-size: 1.15rem;
        }

        .module-links,
        .route-list,
        .model-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .route-list {
            margin-top: 16px;
        }

        .route-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 999px;
            background: #f8fafc;
            color: var(--brand-slate);
            border: 1px solid rgba(148, 163, 184, 0.2);
            font-size: 0.82rem;
        }

        .method-badge {
            padding: 4px 7px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.14);
            color: var(--brand-primary-deep);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .pill {
            background: rgba(15, 118, 110, 0.12);
            color: var(--brand-primary-deep);
        }

        .pill.alt {
            background: rgba(249, 115, 22, 0.14);
            color: #c2410c;
        }

        .model-tags {
            margin-top: 18px;
        }

        .model-tag {
            padding: 10px 12px;
            border-radius: 14px;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: var(--brand-slate);
        }

        @media (max-width: 1080px) {
            .admin-shell,
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: static;
                height: auto;
            }
        }

        @media (max-width: 720px) {
            .admin-main,
            .admin-sidebar {
                padding: 18px;
            }

            .topbar,
            .stat-card,
            .hero-card,
            .module-card,
            .model-group {
                border-radius: 22px;
            }

            .topbar,
            .section-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar-actions,
            .stats-grid {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $stats = $dashboardData['stats'];
        $modules = $dashboardData['modules'];
        $modelGroups = $dashboardData['model_groups'];
        $adminInitials = collect(explode(' ', trim($admin->name)))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('');
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div class="brand-mark">FP</div>
                <div>
                    <div style="font-size: 1.2rem; font-weight: 700;">FitPaxPro</div>
                    <p class="muted" style="color: #94a3b8;">Super Admin Console</p>
                </div>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-label">Workspace</p>
                <a class="sidebar-link" href="#overview">
                    <span>Dashboard Overview</span>
                    <span class="sidebar-badge">{{ $stats['module_count'] }}</span>
                </a>
                <a class="sidebar-link" href="#platform-modules">
                    <span>Installed Modules</span>
                    <span class="sidebar-badge">{{ $stats['web_module_count'] }}</span>
                </a>
                <a class="sidebar-link" href="#domain-models">
                    <span>Domain Models</span>
                    <span class="sidebar-badge">{{ $stats['model_count'] }}</span>
                </a>
            </div>

            <div class="sidebar-group">
                <p class="sidebar-label">Modules</p>
                @foreach ($modules as $module)
                    <a class="sidebar-link" href="#module-{{ $module['id'] }}">
                        <span>{{ $module['name'] }}</span>
                        <span class="sidebar-badge">{{ count($module['api_routes']) + count($module['web_routes']) }}</span>
                    </a>
                @endforeach
            </div>

            <div class="sidebar-group">
                <p class="sidebar-label">Model Collections</p>
                @foreach ($modelGroups as $group)
                    <a class="sidebar-link" href="#group-{{ $group['id'] }}">
                        <span>{{ $group['name'] }}</span>
                        <span class="sidebar-badge">{{ $group['count'] }}</span>
                    </a>
                @endforeach
            </div>
        </aside>

        <main class="admin-main">
            <header class="topbar" id="overview">
                <div>
                    <p class="chip" style="margin: 0 0 12px; width: fit-content;">FitPaxPro operational overview</p>
                    <h1 class="page-title">Super Admin Dashboard</h1>
                    <p class="page-subtitle">A live map of the current FitPaxPro modules, endpoints, and domain models already present in this system.</p>
                </div>

                <div class="topbar-actions">
                    <div class="chip">API endpoints: {{ $stats['api_endpoint_count'] }}</div>
                    <div class="account-card">
                        <div class="avatar">{{ $adminInitials ?: 'SA' }}</div>
                        <div>
                            <div style="font-weight: 700;">{{ $admin->name }}</div>
                            <div class="muted">{{ $admin->email }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Logout</button>
                    </form>
                </div>
            </header>

            <section class="dashboard-grid">
                <article class="hero-card">
                    <span class="hero-kicker">System-aligned dashboard</span>
                    <h2 class="hero-title">Built only from existing FitPaxPro modules and models</h2>
                    <p style="max-width: 640px; color: rgba(248, 250, 252, 0.82);">
                        This dashboard avoids backend changes and reflects what the codebase already exposes today, including installed modules, web entries, API endpoints, and model collections.
                    </p>

                    <div class="hero-meta">
                        <span class="pill">Modules: {{ $stats['module_count'] }}</span>
                        <span class="pill">Web entries: {{ $stats['web_module_count'] }}</span>
                        <span class="pill">Models: {{ $stats['model_count'] }}</span>
                        <span class="pill">Admin type: {{ (int) $admin->user_type }}</span>
                    </div>
                </article>

                <div class="stats-grid">
                    <article class="stat-card">
                        <div class="muted">Installed modules</div>
                        <div class="stat-value">{{ $stats['module_count'] }}</div>
                        <div class="muted">Detected from `Modules/*/module.json`</div>
                    </article>
                    <article class="stat-card">
                        <div class="muted">Accessible web entries</div>
                        <div class="stat-value">{{ $stats['web_module_count'] }}</div>
                        <div class="muted">Existing web routes discovered from module route files</div>
                    </article>
                    <article class="stat-card">
                        <div class="muted">API endpoints</div>
                        <div class="stat-value">{{ $stats['api_endpoint_count'] }}</div>
                        <div class="muted">Counted only from current route definitions</div>
                    </article>
                    <article class="stat-card">
                        <div class="muted">Domain models</div>
                        <div class="stat-value">{{ $stats['model_count'] }}</div>
                        <div class="muted">Grouped from the existing `app/Models` directory</div>
                    </article>
                </div>
            </section>

            <section id="platform-modules">
                <div class="section-heading">
                    <div>
                        <h2>Installed Modules</h2>
                        <p class="muted">Every card below comes from an installed module and its current web or API route files.</p>
                    </div>
                </div>

                <div class="modules-grid">
                    @foreach ($modules as $module)
                        <article class="module-card" id="module-{{ $module['id'] }}">
                            <div class="module-top">
                                <div>
                                    <p class="muted" style="margin-bottom: 8px;">{{ strtoupper($module['alias']) }}</p>
                                    <h3 class="module-title">{{ $module['name'] }}</h3>
                                </div>
                                <div class="module-links">
                                    @if ($module['supports_web'])
                                        <span class="pill">Web</span>
                                    @endif
                                    @if ($module['supports_api'])
                                        <span class="pill alt">API</span>
                                    @endif
                                </div>
                            </div>

                            <p class="muted">{{ $module['description'] }}</p>

                            <div class="module-links" style="margin-top: 18px;">
                                @if ($module['web_url'])
                                    <a class="action-link" href="{{ $module['web_url'] }}">Open module</a>
                                @else
                                    <span class="chip">No direct web entry detected</span>
                                @endif
                            </div>

                            @if (! empty($module['web_routes']))
                                <div class="route-list">
                                    @foreach ($module['web_routes'] as $route)
                                        <span class="route-pill">
                                            <span class="method-badge">{{ $route['method'] }}</span>
                                            <span>{{ $route['path'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if (! empty($module['api_routes']))
                                <div class="route-list">
                                    @foreach ($module['api_routes'] as $route)
                                        <span class="route-pill">
                                            <span class="method-badge">{{ $route['method'] }}</span>
                                            <span>{{ $route['path'] }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="domain-models">
                <div class="section-heading">
                    <div>
                        <h2>Domain Model Collections</h2>
                        <p class="muted">Grouped from the current `app/Models` inventory so super admins can see the platform surface at a glance.</p>
                    </div>
                </div>

                <div class="models-grid">
                    @foreach ($modelGroups as $group)
                        <article class="model-group" id="group-{{ $group['id'] }}">
                            <div class="module-top">
                                <div>
                                    <h3 class="model-title">{{ $group['name'] }}</h3>
                                    <p class="muted">{{ $group['count'] }} existing models in this collection</p>
                                </div>
                                <span class="pill">{{ $group['count'] }} models</span>
                            </div>

                            <div class="model-tags">
                                @foreach ($group['models'] as $model)
                                    <span class="model-tag">{{ $model }}</span>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </main>
    </div>
@endsection
