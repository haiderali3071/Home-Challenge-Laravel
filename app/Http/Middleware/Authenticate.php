<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;


class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (Auth::guard('api')->check()) {
           return $next($request);
        }
        else{
            return response()->json(['message' => 'unauthorized'], 401);
        }
    }
}
