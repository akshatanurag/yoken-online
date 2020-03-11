<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class InstituteOrAdmin
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
        if((Auth::guard('institute')->check()) || (Auth::check() && Auth::user()->role==0 ))
            return $next($request);
        return response('Whoops! Looks like you\'re not authorized to access this page!', 401);
    }
}
