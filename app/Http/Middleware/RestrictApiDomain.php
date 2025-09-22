<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictApiDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedDomains = [
            'lingomasters.ir',
            'api.lingomasters.ir',
            'store.lingomasters.ir',
            'professor.lingomasters.ir',
        ];

        if (!in_array($request->getHost(), $allowedDomains)) {
            return response()->json(['message' => 'Unauthorized domain'], 403);
        }

        return $next($request);
    }
}
