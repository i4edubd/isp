<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WebAuthnController;

/*
|--------------------------------------------------------------------------
| WebAuthn Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/webauthn/register/options', [WebAuthnController::class, 'generateRegistrationOptions'])->name('webauthn.register.options');
    Route::post('/webauthn/register', [WebAuthnController::class, 'register'])->name('webauthn.register');
});

Route::post('/webauthn/login/options', [WebAuthnController::class, 'generateLoginOptions'])->name('webauthn.login.options');
Route::post('/webauthn/login', [WebAuthnController::class, 'login'])->name('webauthn.login');
