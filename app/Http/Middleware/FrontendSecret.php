<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontendSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('X-Frontend-Secret') !== '9f3b6a2c8e1d4f5b7c0a9e6d3b2f1c8a4d7e0b9c6a1f2d3e4b5c6a7d8e9f0a1') {
            return response()->view('errors.frontend-secret'); // فایل ویو اختصاصی
        }

        return $next($request);
    }

}
