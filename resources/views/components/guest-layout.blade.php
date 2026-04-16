<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'FitPaxPro' }} - Premium Auth</title>

    @include('layouts.common.styles-lib')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="auth-container">
        <!-- Left Side: Image & Content -->
        <div class="auth-image-side">
            <img src="{{ asset('assets/images/auth-bg.png') }}" alt="Auth Background" class="auth-bg-img">
            <div class="auth-overlay-content">
                <div class="glass-tag">PREMIUM EXPERIENCE</div>
                <h1>Empower Your<br>Fitness Journey</h1>
                <p>Join over 5,000+ fitness professionals and gym owners managing their growth with FitPaxPro.</p>
            </div>
        </div>

        <!-- Right Side: Interaction Form -->
        <div class="auth-form-side">
            {{ $slot }}
        </div>
    </div>

    @include('layouts.common.scripts-lib')

    <script>
        // Toastr notifications (fallback if not in scripts-lib)
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>
    
    @stack('scripts')
</body>
</html>
