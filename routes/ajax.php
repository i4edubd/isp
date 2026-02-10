<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| AJAX Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// This route is designed to be hit by external services, so it does not
// have the 'web' middleware group (e.g., CSRF protection) applied.
Route::post('/payment/webhook/{gateway}', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');
