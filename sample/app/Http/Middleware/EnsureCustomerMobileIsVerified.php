<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCustomerMobileIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if ($request->user('customer')) {
            if ($request->user('customer')->verified_mobile == 0) {
                return redirect()->route('customer.mobile.verification');
            }
        }

        return $next($request);
    }
}
