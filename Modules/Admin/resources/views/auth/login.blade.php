@extends('admin::components.layouts.master')

@section('content')
    <div style="max-width: 420px; margin: 60px auto; padding: 32px; border: 1px solid #d6d6d6; border-radius: 12px; background: #ffffff;">
        <h1 style="margin: 0 0 8px; font-size: 28px;">Admin Login</h1>
        <p style="margin: 0 0 24px; color: #666666;">Sign in to access the admin dashboard.</p>

        @if ($errors->any())
            <div style="margin-bottom: 16px; padding: 12px; border-radius: 8px; background: #fdeaea; color: #a12622;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div style="margin-bottom: 16px;">
                <label for="email" style="display: block; margin-bottom: 6px; font-weight: 600;">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    style="width: 100%; padding: 12px; border: 1px solid #c8c8c8; border-radius: 8px;"
                >
            </div>

            <div style="margin-bottom: 16px;">
                <label for="password" style="display: block; margin-bottom: 6px; font-weight: 600;">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    style="width: 100%; padding: 12px; border: 1px solid #c8c8c8; border-radius: 8px;"
                >
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: inline-flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="remember" value="1">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" style="width: 100%; padding: 12px; border: 0; border-radius: 8px; background: #111827; color: #ffffff; font-weight: 600; cursor: pointer;">
                Login
            </button>
        </form>
    </div>
@endsection
