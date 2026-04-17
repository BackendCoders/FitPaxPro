@props(['title' => 'FitPaxPro'])
<!doctype html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="FitPaxPro Panel" name="description" />

    @include('layouts.common.styles-lib')
    @stack('styles-lib')

    <style>
        /* Industrial Toastr Overhaul - Forced Dark */
        #toast-container > div.toast, 
        .toast {
            background-color: #121418 !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            box-shadow: 0 10px 50px rgba(0,0,0,0.8) !important;
            border-radius: 14px !important;
            opacity: 1 !important;
            color: #ffffff !important;
            padding: 16px 20px 16px 54px !important;
            background-image: none !important; /* Remove default icons if they clash */
        }
        #toast-container > .toast-success, .toast-success { color: #00ff80 !important; border-left: 5px solid #00ff80 !important; }
        #toast-container > .toast-error, .toast-error { color: #E11218 !important; border-left: 5px solid #E11218 !important; }
        #toast-container > .toast-warning, .toast-warning { color: #ffc107 !important; border-left: 5px solid #ffc107 !important; }
        
        .toast-title { font-weight: 800 !important; color: #fff !important; margin-bottom: 4px !important; display: block; }
        .toast-message { color: rgba(255,255,255,0.8) !important; font-size: 0.85rem !important; font-weight: 600 !important; }
        
        #toast-container .toast-progress { background-color: #E11218 !important; opacity: 0.5 !important; height: 3px !important; }

        /* SweetAlert2 Industrial Dark Overhaul */
        .swal2-popup {
            background: #121418 !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
            border-radius: 20px !important;
            color: #fff !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8) !important;
        }
        .swal2-title { color: #fff !important; font-weight: 800 !important; font-family: 'Inter', sans-serif !important; }
        .swal2-html-container { color: rgba(255,255,255,0.6) !important; font-size: 0.9rem !important; }
        .swal2-confirm { background-color: #E11218 !important; border-radius: 12px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 1px !important; }
        .swal2-cancel { background-color: #23282e !important; border-radius: 12px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 1px !important; }
        .swal2-icon { border-color: rgba(255,255,255,0.1) !important; }
    </style>

    @stack('styles')
</head>

<body data-menu-color="dark" data-sidebar="default" data-topbar-color="dark">

    <div id="app-layout">

        @include('layouts.partials.header')

        @include('layouts.partials.sidebar')

        <div class="content-page">
            <div class="content">

                {{ $slot }}

            </div>

            @include('layouts.partials.footer')

        </div>
    </div>

    @include('layouts.common.scripts-lib')
    @stack('scripts-lib')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {

                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');

                }, false);
            });

        });
    </script>

    <script>
        @if (session('success') || session('error') || $errors->any())
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": 5000
            };
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('submit', function(e) {
                if (e.target.classList.contains('delete-form')) {
                    e.preventDefault();

                    const form = e.target;

                    Swal.fire({
                        title: 'Confirm Deletion',
                        text: "This operation will permanently purge the record from the core mesh.",
                        icon: 'warning',
                        iconColor: '#E11218',
                        showCancelButton: true,
                        confirmButtonColor: '#E11218',
                        cancelButtonColor: '#23282e',
                        confirmButtonText: 'PURGE RECORD',
                        cancelButtonText: 'ABORT',
                        background: '#121418',
                        color: '#fff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>

    @stack('scripts')

</body>

</html>
