<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShowPendingTransaction
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
            if ($request->user()->role !== 'super_admin') {
                if (count($request->user()->payment_sends)) {
                    return redirect()->route('pending_transactions.index');
                }
                if (count($request->user()->payment_receives)) {
                    return redirect()->route('pending_transactions.index');
                }
            }
        }

        return $next($request);
    }
}
