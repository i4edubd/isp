<?php

use App\Http\Controllers\Auth\CardDistributorsAuthenticatedSessionController;
use App\Http\Controllers\CardDistributorsChangePasswordController;
use App\Http\Controllers\CardDistributorsDashboardController;
use App\Http\Controllers\CustomerRechargeByCardDistributorsController;
use App\Http\Controllers\CustomersSearchByCardDistributorsController;
use App\Http\Controllers\ListMobilesForCardDistributorsController;
use App\Http\Controllers\PayBillByCardDistributorsController;
use App\Http\Controllers\PaymentHistoryForCardDistributors;
use App\Http\Controllers\RechargeHistoryForCardDistributors;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Card Distributors UI Routes
|--------------------------------------------------------------------------
*/

Route::get('/card', [CardDistributorsAuthenticatedSessionController::class, 'create'])
    ->middleware('guestCardDistributor:card')
    ->name('card.login');

Route::post('/card', [CardDistributorsAuthenticatedSessionController::class, 'store'])
    ->middleware('guestCardDistributor:card');

Route::prefix('card')->name('card.')->middleware('authCardDistributor:card')->group(function () {

    Route::post('/logout', [CardDistributorsAuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', CardDistributorsDashboardController::class)->name('dashboard');

    Route::resource('search-customer', CustomersSearchByCardDistributorsController::class)
        ->only(['create', 'store', 'show'])
        ->parameters([
            'search-customer' => 'customer_id'
        ]);

    Route::resource('customer.recharge', CustomerRechargeByCardDistributorsController::class)
        ->only(['create', 'store'])
        ->parameters([
            'customer' => 'customer_id'
        ]);

    Route::resource('customer.pay-bill', PayBillByCardDistributorsController::class)
        ->only(['create', 'store'])
        ->parameters([
            'customer' => 'customer_id'
        ]);

    Route::get('/recharge-history', RechargeHistoryForCardDistributors::class)->name('recharge-history');

    Route::get('/payment-history', PaymentHistoryForCardDistributors::class)->name('payment-history');

    Route::get('/mobiles-list', ListMobilesForCardDistributorsController::class)->name('mobiles-list');

    Route::resource('change-password', CardDistributorsChangePasswordController::class)
        ->only(['create', 'store']);
});
