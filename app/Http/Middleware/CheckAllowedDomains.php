<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAllowedDomains
{
    protected $allowedDomains = [
        'lingomasters.ir',
        'api.lingomasters.ir',
        'store.lingomasters.ir',
        'professor.lingomasters.ir',
    ];

    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('origin') ?? parse_url($request->headers->get('referer'), PHP_URL_HOST);

        if ($origin && !$this->isAllowed($origin)) {
            return response()->json(['message' => 'Unauthorized domain'], 403);
        }

        return $next($request);
    }

    protected function isAllowed($origin)
    {
        foreach ($this->allowedDomains as $allowed) {
            if (str_contains($origin, $allowed)) {
                return true;
            }
        }
        return false;
    }
}

