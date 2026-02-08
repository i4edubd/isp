<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountHolderDetailsController;
use App\Http\Controllers\AccountsDailyReportController;
use App\Http\Controllers\AccountsMonthlyReportController;
use App\Http\Controllers\AccountsReceivableCashOutController;
use App\Http\Controllers\AccountStatementController;
use App\Http\Controllers\CashInController;
use App\Http\Controllers\PendingTransactionController;
use App\Http\Controllers\CashOutController;
use App\Http\Controllers\CashReceivedEntryController;
use App\Http\Controllers\CreditAccountOnlinePaymentController;
use App\Http\Controllers\DebitAccountOnlineRechageController;
use App\Http\Controllers\ExchangeAccountBalanceController;
use App\Http\Controllers\Payment\BkashCheckoutForOperatorsOnlinePaymentController;
use App\Http\Controllers\Payment\BkashTokenizedForOperatorsOnlinePaymentController;
use App\Http\Controllers\Payment\NagadPaymentGatewayForOperatorsOnlinePaymentController;
use App\Http\Controllers\Payment\ShurjoPayForOperatorsOnlinePaymentController;
use App\Http\Controllers\Payment\SslcommerzForOperatorsOnlinePaymentController;

/*
|--------------------------------------------------------------------------
| Accounting Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', '2FA'])->group(function () {

    Route::resource('accounts', AccountController::class)
        ->only(['edit', 'update']);

    Route::get('/accounts/payable', [AccountController::class, 'accountsPayable'])
        ->name('accounts.payable');

    Route::get('/accounts/receivable', [AccountController::class, 'accountsReceivable'])
        ->name('accounts.receivable');

    Route::get('/accounts-monthly-report', [AccountsMonthlyReportController::class, 'index'])
        ->name('accounts.monthly-report');

    Route::get('/accounts-daily-report', [AccountsDailyReportController::class, 'index'])
        ->name('accounts.daily-report');

    Route::resource('entry-for-cash-received', CashReceivedEntryController::class)
        ->only(['create', 'store']);

    Route::resource('accounts_receivable.cash_out', AccountsReceivableCashOutController::class)
        ->only(['create', 'store'])
        ->parameters([
            'accounts_receivable' => 'account'
        ]);

    Route::get('/accounts/{account}/transactions', [AccountController::class, 'transactions'])
        ->name('account.transactions');

    Route::get('/accounts/ins/{account}/{year}/{month}', [AccountController::class, 'cashInDetails'])
        ->name('account.ins');

    Route::resource('cash_ins', CashInController::class)
        ->only(['show']);

    Route::get('/accounts/outs/{account}/{year}/{month}', [AccountController::class, 'cashOutDetails'])
        ->name('account.outs');

    Route::resource('cash_outs', CashOutController::class)
        ->only(['show']);

    Route::resource('pending_transactions', PendingTransactionController::class)
        ->only(['index', 'destroy']);

    Route::get('/pending_transactions/{account}/create', [PendingTransactionController::class, 'create'])
        ->name('pending_transactions.create');

    Route::post('/pending_transactions/{account}/store', [PendingTransactionController::class, 'store'])
        ->name('pending_transactions.store');

    Route::post('/cash_outs/{pending_transaction}/store', [CashOutController::class, 'store'])
        ->name('cash_outs.store');

    Route::resource('account-holder-details', AccountHolderDetailsController::class)
        ->only(['show'])
        ->parameters([
            'account-holder-details' => 'operator'
        ]);

    Route::resource('accounts.statement', AccountStatementController::class)
        ->only(['create', 'store']);

    Route::resource('accounts.exchange', ExchangeAccountBalanceController::class)
        ->only(['create', 'store']);

    // Online Payments
    Route::resource('accounts.OnlineRechage', DebitAccountOnlineRechageController::class)
        ->only(['create', 'store']);

    Route::resource('accounts.OnlinePayment', CreditAccountOnlinePaymentController::class)
        ->only(['create', 'store']);

    // Online Payments With BkashCheckout
    Route::get('bkash/operators_online_payments/{operators_online_payment}', [BkashCheckoutForOperatorsOnlinePaymentController::class, 'initiatePayment'])
        ->name('bkash.operators_online_payment.initiate');

    Route::get('bkash/operators_online_payments/{operators_online_payment}/create', [BkashCheckoutForOperatorsOnlinePaymentController::class, 'createPayment'])
        ->name('bkash.operators_online_payment.create');

    Route::get('bkash/operators_online_payments/{operators_online_payment}/execute', [BkashCheckoutForOperatorsOnlinePaymentController::class, 'executePayment'])
        ->name('bkash.operators_online_payment.execute');

    Route::get('bkash/operators_online_payments/{operators_online_payment}/query', [BkashCheckoutForOperatorsOnlinePaymentController::class, 'queryPayment'])
        ->name('bkash.operators_online_payment.query');

    Route::get('bkash/operators_online_payments/{operators_online_payment}/search', [BkashCheckoutForOperatorsOnlinePaymentController::class, 'searchTransaction'])
        ->name('bkash.operators_online_payment.search');

    Route::get('bkash/operators_online_payments/{operators_online_payment}/success', [BkashCheckoutForOperatorsOnlinePaymentController::class, 'successPayment'])
        ->name('bkash.operators_online_payment.success');
});

// Online Payments With bkash_tokenized_checkout
Route::get('bkash_tokenized_checkout/operators_online_payments/{operators_online_payment}', [BkashTokenizedForOperatorsOnlinePaymentController::class, 'createPayment'])
    ->name('bkash_tokenized_checkout.operators_online_payment.create')
    ->middleware('auth');

Route::get('bkash_tokenized_checkout/operators_online_payment/callback', [BkashTokenizedForOperatorsOnlinePaymentController::class, 'paymentCallback'])
    ->name('bkash_tokenized_checkout.operators_online_payment.callback');

// Online Payments With Nagad
Route::get('nagad/operators_online_payments/{operators_online_payment}', [NagadPaymentGatewayForOperatorsOnlinePaymentController::class, 'initiatePayment'])
    ->name('nagad.operators_online_payment.initiate')
    ->middleware('auth');

Route::get('nagad/operators_online_payment/callback', [NagadPaymentGatewayForOperatorsOnlinePaymentController::class, 'paymentCallback'])
    ->name('nagad.operators_online_payment.callback');

// Online Payments With ShurjoPay
Route::get('ShurjoPay/operators_online_payments/{operators_online_payment}/create', [ShurjoPayForOperatorsOnlinePaymentController::class, 'createPayment'])
    ->name('ShurjoPay.operators_online_payment.create')
    ->middleware('auth');

Route::get('ShurjoPay/operators_online_payment/callback', [ShurjoPayForOperatorsOnlinePaymentController::class, 'successCallback'])
    ->name('ShurjoPay.operators_online_payment.callback');

// Online Payments With Sslcommerz
Route::get('sslcommerz/operators_online_payments/{operators_online_payment}/create', [SslcommerzForOperatorsOnlinePaymentController::class, 'createPayment'])
    ->name('sslcommerz.operators_online_payment.create')
    ->middleware('auth');

Route::post('sslcommerz/operators_online_payment/callback', [SslcommerzForOperatorsOnlinePaymentController::class, 'successCallback'])
    ->name('sslcommerz.operators_online_payment.callback');
