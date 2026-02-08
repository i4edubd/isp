<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Net_IPv4;

class AccessControlList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (strlen(config('acls')[0])) {

            $ip = $request->ip();
            $ipv4lib = new Net_IPv4();

            foreach (config('acls') as $network) {
                if ($ipv4lib->ipInNetwork($ip, $network)) {
                    return $next($request);
                }
            }

            return abort(404);
        }

        return $next($request);
    }
}
