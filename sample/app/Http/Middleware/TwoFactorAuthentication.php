<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TwoFactorAuthentication
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
        if ($request->user()) {
            if ($request->user()->two_factor_challenge_due) {
                return redirect()->route('two-factor.login');
            }
            if ($request->user()->device_identification_pending) {
                return redirect()->route('operators.device-verification.create', ['operator' => $request->user()]);
            }
        }
        return $next($request);
    }
}
