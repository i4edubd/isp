<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\UserInstanceAuthenticateController;
use App\Http\Controllers\AuthenticationLogController;
use App\Http\Controllers\DeviceIdentificationController;
use App\Http\Controllers\DeviceVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');

Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware('auth')
    ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware('auth');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
    ->middleware('auth')
    ->name('two-factor.login');

Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
    ->middleware('auth');

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('/two-factor-authentication/create', [TwoFactorAuthenticationController::class, 'create'])
        ->name('two-factor.create');

    Route::post('/two-factor-authentication/store', [TwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.store');

    Route::post('/two-factor-authentication/delete', [TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.delete');

    Route::resource('authentication_log', AuthenticationLogController::class)
        ->only(['index']);

    Route::resource('operators.device-identification', DeviceIdentificationController::class)
        ->only(['index', 'create', 'store']);

    Route::post('/disable-device-identification', [DeviceIdentificationController::class, 'disable'])->name('disable-device-identification');

    Route::resource('operators.device-verification', DeviceVerificationController::class)
        ->only(['create', 'store']);

    Route::get('/change-password', [ChangePasswordController::class, 'create'])
        ->name('admin.password.change');

    Route::post('/change-password', [ChangePasswordController::class, 'store']);

    Route::resource('/authenticate-operator-instance', UserInstanceAuthenticateController::class)
        ->only(['show', 'store'])
        ->parameters([
            'authenticate-operator-instance' => 'operator'
        ]);
});
