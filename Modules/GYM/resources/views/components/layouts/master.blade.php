<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'FitPax Pro | Gym Dashboard')</title>
        <style>
            :root {
                --bg: #07111f;
                --bg-soft: #0b1729;
                --panel: rgba(14, 24, 41, 0.9);
                --panel-strong: #111f34;
                --panel-border: rgba(148, 163, 184, 0.16);
                --text: #e6edf7;
                --muted: #92a2bd;
                --muted-strong: #bcc7d9;
                --emerald: #34d399;
                --teal: #2dd4bf;
                --amber: #fbbf24;
                --rose: #fb7185;
                --cyan: #38bdf8;
                --shadow: 0 28px 60px rgba(0, 0, 0, 0.32);
                --radius-xl: 28px;
                --radius-lg: 20px;
                --radius-md: 16px;
                --radius-sm: 12px;
                --font: "Segoe UI Variable", "Segoe UI", "Trebuchet MS", "Noto Sans", sans-serif;
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                margin: 0;
                min-height: 100vh;
                color: var(--text);
                font-family: var(--font);
                background:
                    radial-gradient(circle at top left, rgba(45, 212, 191, 0.18), transparent 26%),
                    radial-gradient(circle at top right, rgba(251, 191, 36, 0.14), transparent 22%),
                    linear-gradient(180deg, #050b14 0%, #07111f 44%, #091523 100%);
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                background-image:
                    linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
                background-size: 36px 36px;
                mask-image: linear-gradient(180deg, rgba(0, 0, 0, 0.4), transparent 90%);
                opacity: 0.35;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            button,
            input {
                font: inherit;
            }

            .page-shell {
                width: min(1460px, calc(100% - 32px));
                margin: 0 auto;
                padding: 20px 0 40px;
                position: relative;
                z-index: 1;
            }

            .topbar {
                position: sticky;
                top: 16px;
                z-index: 20;
                display: grid;
                grid-template-columns: auto minmax(0, 1fr) auto;
                gap: 18px;
                align-items: center;
                padding: 16px 18px;
                margin-bottom: 18px;
                border: 1px solid var(--panel-border);
                border-radius: var(--radius-xl);
                background: rgba(8, 15, 27, 0.82);
                box-shadow: var(--shadow);
                backdrop-filter: blur(18px);
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }

            .brand-mark {
                width: 46px;
                height: 46px;
                display: grid;
                place-items: center;
                border-radius: 15px;
                background: linear-gradient(135deg, var(--emerald), var(--cyan));
                color: #04111d;
                font-weight: 900;
                letter-spacing: 0.08em;
                box-shadow: 0 14px 30px rgba(45, 212, 191, 0.2);
                flex: 0 0 auto;
            }

            .brand-copy {
                min-width: 0;
            }

            .brand-copy strong {
                display: block;
                font-size: 1rem;
                line-height: 1.1;
            }

            .brand-copy span {
                display: block;
                color: var(--muted);
                font-size: 0.78rem;
                margin-top: 3px;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-links a {
                padding: 9px 12px;
                border-radius: 999px;
                color: var(--muted-strong);
                font-size: 0.84rem;
                font-weight: 600;
                transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
            }

            .nav-links a:hover,
            .nav-links a.active {
                color: var(--text);
                background: rgba(148, 163, 184, 0.12);
                transform: translateY(-1px);
            }

            .topbar-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .search-pill {
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 250px;
                padding: 10px 14px;
                border-radius: 999px;
                border: 1px solid rgba(148, 163, 184, 0.16);
                background: rgba(15, 23, 42, 0.72);
                color: var(--muted);
            }

            .search-pill input {
                width: 100%;
                border: 0;
                outline: none;
                background: transparent;
                color: var(--text);
            }

            .search-pill input::placeholder {
                color: var(--muted);
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 11px 14px;
                border: 1px solid transparent;
                border-radius: 14px;
                font-size: 0.85rem;
                font-weight: 700;
                cursor: pointer;
                transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
            }

            .btn:hover {
                transform: translateY(-1px);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--emerald), var(--teal));
                color: #04111d;
                box-shadow: 0 14px 24px rgba(45, 212, 191, 0.18);
            }

            .btn-ghost {
                background: rgba(148, 163, 184, 0.08);
                color: var(--text);
                border-color: rgba(148, 163, 184, 0.14);
            }

            .btn-icon {
                width: 42px;
                height: 42px;
                padding: 0;
                border-radius: 14px;
            }

            .content {
                display: block;
            }

            .dashboard {
                display: grid;
                gap: 18px;
            }

            .hero-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.7fr) minmax(310px, 0.9fr);
                gap: 18px;
            }

            .hero-panel,
            .hero-side,
            .panel,
            .stat-card {
                border: 1px solid var(--panel-border);
                background: linear-gradient(180deg, rgba(17, 31, 52, 0.92), rgba(10, 18, 31, 0.95));
                box-shadow: var(--shadow);
                backdrop-filter: blur(16px);
            }

            .hero-panel,
            .hero-side,
            .panel {
                border-radius: var(--radius-xl);
            }

            .hero-panel {
                padding: 26px;
                display: grid;
                gap: 18px;
            }

            .eyebrow-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .eyebrow,
            .panel-kicker,
            .side-label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 0.74rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--muted-strong);
            }

            .status-chip {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(148, 163, 184, 0.08);
                color: var(--muted-strong);
                font-size: 0.78rem;
                font-weight: 700;
            }

            .status-chip-live {
                color: #a7f3d0;
                background: rgba(52, 211, 153, 0.12);
            }

            .hero-panel h1 {
                margin: 0;
                max-width: 16ch;
                font-size: clamp(2rem, 4vw, 3.5rem);
                line-height: 0.98;
                letter-spacing: -0.04em;
            }

            .hero-panel p {
                margin: 0;
                max-width: 62ch;
                color: var(--muted);
                line-height: 1.7;
                font-size: 0.98rem;
            }

            .hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .btn-sm {
                padding: 9px 12px;
                border-radius: 12px;
                font-size: 0.78rem;
            }

            .hero-mini-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }

            .mini-card {
                padding: 16px;
                border-radius: var(--radius-md);
                background: rgba(8, 15, 27, 0.62);
                border: 1px solid rgba(148, 163, 184, 0.12);
            }

            .mini-card span,
            .side-card p,
            .stat-card small,
            .summary-row span,
            .summary-row strong {
                display: block;
            }

            .mini-card span {
                color: var(--muted);
                font-size: 0.78rem;
                margin-bottom: 12px;
            }

            .mini-card strong {
                font-size: 1.55rem;
                letter-spacing: -0.03em;
            }

            .mini-card em {
                display: block;
                margin-top: 6px;
                color: var(--muted-strong);
                font-style: normal;
                font-size: 0.75rem;
            }

            .hero-side {
                padding: 18px;
                display: grid;
                gap: 12px;
            }

            .side-card {
                padding: 18px;
                border-radius: var(--radius-lg);
                background: rgba(8, 15, 27, 0.65);
                border: 1px solid rgba(148, 163, 184, 0.12);
            }

            .side-card strong {
                display: block;
                margin: 10px 0 8px;
                font-size: 1.15rem;
            }

            .side-card p {
                margin: 0;
                color: var(--muted);
                font-size: 0.88rem;
                line-height: 1.55;
            }

            .side-card-accent {
                background: linear-gradient(135deg, rgba(45, 212, 191, 0.18), rgba(56, 189, 248, 0.1));
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 14px;
            }

            .stat-card {
                border-radius: 22px;
                padding: 18px;
                display: grid;
                gap: 8px;
            }

            .stat-card span {
                color: var(--muted);
                font-size: 0.8rem;
                font-weight: 700;
            }

            .stat-card strong {
                font-size: 1.85rem;
                letter-spacing: -0.04em;
            }

            .stat-card small {
                color: var(--muted-strong);
                font-size: 0.78rem;
            }

            .stat-card-emerald {
                box-shadow: inset 0 1px 0 rgba(52, 211, 153, 0.22), var(--shadow);
            }

            .stat-card-cyan {
                box-shadow: inset 0 1px 0 rgba(56, 189, 248, 0.22), var(--shadow);
            }

            .stat-card-amber {
                box-shadow: inset 0 1px 0 rgba(251, 191, 36, 0.22), var(--shadow);
            }

            .stat-card-rose {
                box-shadow: inset 0 1px 0 rgba(251, 113, 133, 0.22), var(--shadow);
            }

            .content-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.4fr) minmax(300px, 0.9fr) minmax(280px, 0.8fr);
                gap: 18px;
            }

            .panel {
                padding: 20px;
            }

            .panel-wide {
                grid-column: span 1;
            }

            .panel-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 16px;
            }

            .panel-head h2 {
                margin: 4px 0 0;
                font-size: 1.15rem;
                letter-spacing: -0.02em;
            }

            .timeline-list,
            .member-list,
            .task-list,
            .summary-stack {
                display: grid;
                gap: 10px;
            }

            .timeline-item {
                display: grid;
                grid-template-columns: 70px minmax(0, 1fr) auto;
                gap: 12px;
                align-items: center;
                padding: 14px 0;
                border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            }

            .timeline-item:last-child {
                border-bottom: 0;
                padding-bottom: 2px;
            }

            .timeline-time {
                color: #a7f3d0;
                font-size: 0.82rem;
                font-weight: 800;
                letter-spacing: 0.08em;
            }

            .timeline-body strong,
            .member-copy strong,
            .alert-card strong,
            .summary-row strong {
                display: block;
                font-size: 0.98rem;
            }

            .timeline-body span,
            .member-copy span {
                display: block;
                margin-top: 4px;
                color: var(--muted);
                font-size: 0.82rem;
            }

            .timeline-meta span,
            .member-status {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 7px 10px;
                border-radius: 999px;
                font-size: 0.75rem;
                font-weight: 700;
                background: rgba(148, 163, 184, 0.08);
                color: var(--muted-strong);
                white-space: nowrap;
            }

            .member-row {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr) auto;
                gap: 12px;
                align-items: center;
                padding: 12px 0;
                border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            }

            .member-row:last-child {
                border-bottom: 0;
                padding-bottom: 2px;
            }

            .avatar {
                width: 38px;
                height: 38px;
                display: grid;
                place-items: center;
                border-radius: 13px;
                font-weight: 800;
                color: #04111d;
            }

            .avatar-emerald {
                background: linear-gradient(135deg, #34d399, #a7f3d0);
            }

            .avatar-amber {
                background: linear-gradient(135deg, #fbbf24, #fde68a);
            }

            .avatar-cyan {
                background: linear-gradient(135deg, #38bdf8, #bfdbfe);
            }

            .avatar-rose {
                background: linear-gradient(135deg, #fb7185, #fda4af);
            }

            .status-emerald {
                color: #86efac;
                background: rgba(52, 211, 153, 0.12);
            }

            .status-amber {
                color: #fde68a;
                background: rgba(251, 191, 36, 0.12);
            }

            .status-cyan {
                color: #bae6fd;
                background: rgba(56, 189, 248, 0.12);
            }

            .status-rose {
                color: #fda4af;
                background: rgba(251, 113, 133, 0.12);
            }

            .task-list {
                padding-left: 18px;
                margin: 0;
            }

            .task-list li {
                color: var(--text);
                line-height: 1.55;
                padding-left: 4px;
            }

            .alert-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
            }

            .alert-card {
                padding: 16px;
                border-radius: var(--radius-md);
                background: rgba(8, 15, 27, 0.66);
                border: 1px solid rgba(148, 163, 184, 0.12);
            }

            .alert-card span {
                color: var(--muted);
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .alert-card strong {
                margin-top: 10px;
                font-size: 1.35rem;
            }

            .alert-card p {
                margin: 8px 0 0;
                color: var(--muted);
                font-size: 0.86rem;
                line-height: 1.5;
            }

            .summary-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 14px 0;
                border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            }

            .summary-row:last-child {
                border-bottom: 0;
                padding-bottom: 0;
            }

            .summary-row span {
                color: var(--muted);
                font-size: 0.86rem;
            }

            .summary-row strong {
                color: var(--text);
                font-size: 0.95rem;
                text-align: right;
            }

            @media (max-width: 1180px) {
                .topbar {
                    grid-template-columns: 1fr;
                }

                .nav-links,
                .topbar-actions {
                    justify-content: flex-start;
                }

                .search-pill {
                    min-width: 0;
                    width: 100%;
                }

                .hero-grid,
                .content-grid,
                .stats-grid,
                .alert-grid,
                .hero-mini-grid {
                    grid-template-columns: 1fr;
                }

                .content-grid {
                    grid-template-columns: 1fr;
                }

                .hero-grid {
                    grid-template-columns: 1fr;
                }

                .timeline-item,
                .member-row {
                    grid-template-columns: 1fr;
                    align-items: flex-start;
                }

                .timeline-meta,
                .member-status {
                    justify-self: flex-start;
                }
            }

            @media (max-width: 720px) {
                .page-shell {
                    width: min(100% - 18px, 100%);
                    padding-top: 12px;
                }

                .topbar {
                    top: 8px;
                    padding: 14px;
                    border-radius: 22px;
                }

                .hero-panel,
                .panel,
                .hero-side {
                    padding: 18px;
                }

                .hero-panel h1 {
                    max-width: 100%;
                }

                .hero-mini-grid,
                .alert-grid {
                    grid-template-columns: 1fr;
                }

                .stats-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
        </style>

       {{-- Laravel Vite - CSS File --}}
       {{-- {{ module_vite('build-gym', 'Resources/assets/sass/app.scss') }} --}}

    </head>
    <body>
        <div class="page-shell">
            @yield('content')
        </div>

        {{-- Laravel Vite - JS File --}}
        {{-- {{ module_vite('build-gym', 'Resources/assets/js/app.js') }} --}}
    </body>
</html>
