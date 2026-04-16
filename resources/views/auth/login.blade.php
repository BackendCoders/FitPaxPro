<x-guest-layout title="Login">
    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="FitPaxPro" class="auth-logo" onerror="this.src='https://ui-avatars.com/api/?name=F+P&background=6366f1&color=fff'">
            <h2>Welcome Back</h2>
            <p>Please enter your details to sign in</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="admin@fitpaxpro.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="password" style="margin-bottom: 0;">Password</label>
                    <a href="#" style="font-size: 0.8rem; color: #6366f1; text-decoration: none;">Forgot password?</a>
                </div>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
                <input type="checkbox" name="remember" id="remember" style="width: 16px; height: 16px; cursor: pointer;">
                <label for="remember" style="font-size: 0.9rem; color: #64748b; margin-bottom: 0; cursor: pointer;">Remember me</label>
            </div>

            <button type="submit" class="btn-auth">Sign In</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Create an account</a>
        </div>
    </div>
</x-guest-layout>
