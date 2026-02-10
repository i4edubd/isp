<?php

namespace App\Http\Controllers;

use App\Models\failed_login;
use Illuminate\Http\Request;

class FailedLoginViewController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $failed_logins = failed_login::all(['id', 'guard', 'email', 'password', 'created_at']);
        return view('admins.developer.failed-logins', ['failed_logins' => $failed_logins]);
    }
}
