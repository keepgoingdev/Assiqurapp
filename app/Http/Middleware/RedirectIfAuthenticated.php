<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
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
        if (Auth::guard($guard)->check()) {

            if (strpos($request->getPathInfo(), '/admin') === 0) {
                // It starts with 'http'
                return redirect(route('admin.sales'));
            }
            return redirect('/registersale');

        }

        return $next($request);
    }
}
