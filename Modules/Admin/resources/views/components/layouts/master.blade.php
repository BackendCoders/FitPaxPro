<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FitPaxPro Admin</title>

       {{-- Laravel Vite - CSS File --}}
       {{-- {{ module_vite('build-admin', 'Resources/assets/sass/app.scss') }} --}}

    </head>
    <body style="margin: 0; font-family: Arial, sans-serif; background: #f4f6f8; color: #111827;">
        @yield('content')

        {{-- Laravel Vite - JS File --}}
        {{-- {{ module_vite('build-admin', 'Resources/assets/js/app.js') }} --}}
    </body>
</html>
