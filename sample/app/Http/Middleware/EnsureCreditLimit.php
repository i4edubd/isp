<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCreditLimit
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
            if (($request->user()->role === 'operator' || $request->user()->role === 'sub_operator') && $request->user()->account_type === 'credit' && $request->user()->credit_limit > 1) {
                if ($request->user()->credit_balance < 1) {
                    return redirect()->route('accounts.payable')->with('error', 'Please Send Money to your upstream first!');
                }
            }
        }
        return $next($request);
    }
}
