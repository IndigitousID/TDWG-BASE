<?php

namespace App\Http\Middleware;

use Closure, Str;
use Illuminate\Support\Facades\Auth;

class Bypass
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::user()) {
            Auth::loginUsingId(1);
        }

        return $next($request);
    }
}
