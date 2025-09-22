<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfessorIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // اطمینان از اینکه کاربر استاد هست و تایید شده
        if (!$user || !$user->professor || $user->professor->status !== 'approved') {
            return api_response(null,'دسترسی فقط برای اساتید تایید شده امکان‌پذیر است.');
        }


        return $next($request);
    }
}
