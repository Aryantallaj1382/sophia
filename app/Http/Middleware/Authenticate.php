<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    protected function unauthenticated($request, array $guards)
    {
        // همیشه JSON 401 برگردون
        abort(response()->json([
            'message' => 'Unauthorized'
        ], 401));
    }
}
