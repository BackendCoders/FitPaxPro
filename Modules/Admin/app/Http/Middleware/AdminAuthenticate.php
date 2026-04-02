<?php

namespace Modules\Admin\app\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        /** @var User $admin */
        $admin = Auth::guard('admin')->user();

        if (! $this->isAdmin($admin)) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors(['email' => 'You are not authorized to access the admin dashboard.']);
        }

        return $next($request);
    }

    protected function isAdmin(User $user): bool
    {
        if ((int) $user->user_type === 0) {
            return true;
        }

        return $user->hasAnyRole(['admin', 'super-admin', 'super admin']);
    }
}
