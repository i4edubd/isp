<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSmsPayment
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
        if (config('consumer.payment_gateway_temporary_failure')) {
            return $next($request);
        }
        if ($request->user()) {
            if (count($request->user()->sms_bills)) {
                return redirect()->route('sms_bills.index');
            }
        }
        return $next($request);
    }
}
