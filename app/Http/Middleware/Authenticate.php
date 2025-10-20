<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;use Illuminate\Support\Facades\Auth;


class Authenticate
{
    protected function unauthenticated($request, array $guards)
    {
        // همیشه JSON 401 برگردون
        abort(response()->json([
            'message' => 'Unauthorized'
        ], 401));
    }
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // بررسی guard ها (در اینجا از web استفاده می‌کنیم)
        if (! Auth::guard('web')->check()) {
            return $this->redirectTo($request);
        }

        return $next($request);
    }

    /**
     * مسیر لاگین برای redirect.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('admin.login');
            }

            return redirect()->route('login');
        }

        // اگر JSON بخواهد، 401
        abort(response()->json(['message' => 'Unauthorized'], 401));
    }
}
