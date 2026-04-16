<x-guest-layout title="Register">
    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="FitPaxPro" class="auth-logo" onerror="this.src='https://ui-avatars.com/api/?name=F+P&background=6366f1&color=fff'">
            <h2>Create Account</h2>
            <p>Join our community by filling the form below</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
            </div>

            <div style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 25px;">
                <input type="checkbox" name="terms" id="terms" style="width: 16px; height: 16px; cursor: pointer; margin-top: 3px;" required>
                <label for="terms" style="font-size: 0.85rem; color: #64748b; margin-bottom: 0; cursor: pointer;">
                    I agree to the <a href="#" style="color: #6366f1;">Terms of Service</a> and <a href="#" style="color: #6366f1;">Privacy Policy</a>.
                </label>
            </div>

            <button type="submit" class="btn-auth">Register Now</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>
</x-guest-layout>
