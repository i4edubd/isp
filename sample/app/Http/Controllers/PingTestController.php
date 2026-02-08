<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PingTestController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $response = null;

        if ($request->filled('response')) {
            $response = encryptOrDecrypt('decrypt', $request->response);
        }

        return match ($request->user()->role) {
            'group_admin' => view('admins.group_admin.ping-test', ['response' => $response]),
        };
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ipv4',
        ]);

        $ip_address = $request->ip_address;
        $output = null;

        if (RateLimiter::tooManyAttempts('ping-test:' . $request->user()->id, 2)) {
            $seconds = RateLimiter::availableIn('ping-test:' . $request->user()->id);
            $output = ['Too many ping test!'];
            $output[] = 'You may try again in ' . $seconds . ' seconds.';
        } else {
            RateLimiter::hit('ping-test:' . $request->user()->id);
            exec("ping -c 4 $ip_address", $output);
        }

        $response = encryptOrDecrypt('encrypt', $output);

        return redirect()->route('ping-test.create', ['response' => $response]);
    }
}
