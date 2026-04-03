<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'FitPaxPro Admin')</title>

       {{-- Laravel Vite - CSS File --}}
       {{-- {{ module_vite('build-admin', 'Resources/assets/sass/app.scss') }} --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
        <style>
            :root {
                --brand-ink: #0f172a;
                --brand-slate: #334155;
                --brand-muted: #64748b;
                --brand-line: rgba(148, 163, 184, 0.22);
                --brand-surface: rgba(255, 255, 255, 0.88);
                --brand-surface-strong: #ffffff;
                --brand-wash: #f8fafc;
                --brand-primary: #0f766e;
                --brand-primary-deep: #115e59;
                --brand-accent: #f97316;
                --brand-shadow: 0 28px 60px rgba(15, 23, 42, 0.12);
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
                background:
                    radial-gradient(circle at top left, rgba(15, 118, 110, 0.18), transparent 32%),
                    radial-gradient(circle at top right, rgba(249, 115, 22, 0.14), transparent 24%),
                    linear-gradient(180deg, #f8fafc 0%, #eff6ff 52%, #f8fafc 100%);
                color: var(--brand-ink);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            button,
            input {
                font: inherit;
            }
        </style>
        @stack('styles')

    </head>
    <body>
        @yield('content')

        {{-- Laravel Vite - JS File --}}
        {{-- {{ module_vite('build-admin', 'Resources/assets/js/app.js') }} --}}
    </body>
</html>
