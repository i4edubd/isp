<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\AuthenticationLogJob;
use App\Models\operator;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $host = $request->getHttpHost();

        $sub_domains = explode('.', $host);

        if (count($sub_domains) >= 2 && count($sub_domains) <= 3) {
            $index = count($sub_domains) - 2;
            $host = $sub_domains[$index];
        }

        return view('admins.login.login3', [
            'host' => $host,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {

        if (config('recaptchav3.enabled')) {
            $request->validate([
                'g-recaptcha-response' => 'required|string',
            ]);
            $score = RecaptchaV3::verify($request->get('g-recaptcha-response'), 'login');
            if ($score < 0.5) {
                return redirect()->route('login')->with('info', 'reCAPTCHA verification failed');
            }
        }

        $request->authenticate();

        $request->session()->regenerate();

        $operator = Auth::user();
        $operator = operator::find($operator->id);
        if ($operator->two_factor_activated) {
            $operator->two_factor_challenge_due = 1;
        } else {
            $operator->two_factor_challenge_due = 0;
        }
        $operator->save();

        AuthenticationLogJob::dispatch('login', $operator, $request->ip(), $request->userAgent())
            ->onConnection('database')
            ->onQueue('default');

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
