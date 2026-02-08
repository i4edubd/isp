<?php

use App\Http\Controllers\Ajax\AjaxTimeZoneController;
use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AJAX Routes
|--------------------------------------------------------------------------
*/

Route::prefix('ajax')->group(function () {
    Route::get('/timezones', [AjaxTimeZoneController::class, 'getTimeZones'])
        ->name('ajax.timezones');

    Route::get('/console-log/{token}/{log}', function ($token, $log) {
        if ($token == csrf_token()) {
            Log::channel('debug')->debug($log);
        }
    })->name('console.log');
});

Route::prefix('ajax')->middleware(['auth'])->group(function () {
    Route::get('/all-customers-row/{customer}', [CustomerController::class, 'getRow'])
        ->name('ajax.all-customers.row');

    Route::get('show-runtime-invoice-for-generate-bill-action/{customer}/{billing_period}', [CustomerBillGenerateController::class, 'showRuntimeInvoice'])
        ->name('ajax.runtime-invoice-for-generate-bill-action');
});
