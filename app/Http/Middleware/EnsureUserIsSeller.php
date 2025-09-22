<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSeller
{
//    public function handle(Request $request, Closure $next): Response
//    {
//        $user = $request->user();
//
//        if (!$user || $user->role !== 'seller') {
//            return response()->json([
//                'message' => 'شما مجاز به دسترسی به این بخش نیستید.'
//            ], 403);
//        }
//
//        return $next($request);
//    }
}
