@extends('admin::components.layouts.master')

@section('content')
    <div style="max-width: 900px; margin: 40px auto; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div>
                <h1 style="margin: 0 0 8px; font-size: 30px;">Admin Dashboard</h1>
                <p style="margin: 0; color: #666666;">Welcome back, {{ $admin->name }}.</p>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" style="padding: 10px 16px; border: 0; border-radius: 8px; background: #b91c1c; color: #ffffff; cursor: pointer;">
                    Logout
                </button>
            </form>
        </div>

        <div style="padding: 24px; border: 1px solid #d6d6d6; border-radius: 12px; background: #ffffff;">
            <h2 style="margin-top: 0;">Account Summary</h2>
            <p style="margin: 0 0 8px;"><strong>Name:</strong> {{ $admin->name }}</p>
            <p style="margin: 0 0 8px;"><strong>Email:</strong> {{ $admin->email }}</p>
            <p style="margin: 0;"><strong>User Type:</strong> {{ (int) $admin->user_type }}</p>
        </div>
    </div>
@endsection
