<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountBalance
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
            if (($request->user()->role === 'operator' || $request->user()->role === 'sub_operator') && $request->user()->account_type === 'debit') {
                if ($request->user()->account_balance < 1) {
                    return redirect()->route('accounts.receivable')->with('error', 'Please add account balance first!');
                }
            }
        }
        return $next($request);
    }
}
