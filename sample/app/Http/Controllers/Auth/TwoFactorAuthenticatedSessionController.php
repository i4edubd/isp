<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticatedSessionController extends Controller
{
    /**
     * Display the Two Factor Challange Form.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('admins.components.two-factor-challenge');
    }

    /**
     * Handle the two factor login request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $operator = $request->user();
        $google2fa = new Google2FA();
        $code = $request->input('code');
        $valid = $google2fa->verifyKey($operator->two_factor_secret, $code);
        if ($valid) {
            $request->user()->two_factor_challenge_due = 0;
            $request->user()->save();
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('two-factor.login')->with('status', 'Two Factor Authentication Failed');
        }
    }
}
