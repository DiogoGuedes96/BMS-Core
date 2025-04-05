<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Allow all CORS requests
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*', true)
            ->header('Access-Control-Allow-Methods', '*', true)
            ->header('Access-Control-Allow-Headers', '*', true);
    }
}
