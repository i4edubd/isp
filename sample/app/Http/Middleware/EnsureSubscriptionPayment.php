<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class EnsureSubscriptionPayment
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
            if ($request->user()->role === 'group_admin' && $request->user()->subscription_status === 'suspended') {
                return redirect()->route('subscription_bills.index');
            }
        }
        return $next($request);
    }
}
