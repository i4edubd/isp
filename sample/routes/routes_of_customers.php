<?php

use App\Http\Controllers\CardRechargeController;
use App\Http\Controllers\ComplaintCustomerInterfaceController;
use App\Http\Controllers\Customer\CustomerIdRecoveryController;
use App\Http\Controllers\Customer\CustomerMobileVerificationController;
use App\Http\Controllers\Customer\CustomerPackagePurchaseController;
use App\Http\Controllers\Customer\CustomersMacAddressReplaceController;
use App\Http\Controllers\Customer\CustomersPayBillController;
use App\Http\Controllers\Customer\CustomersWebInterfaceController;
use App\Http\Controllers\Customer\CustomerWebLoginController;
use App\Http\Controllers\Customer\HotspotLoginController;
use App\Http\Controllers\Customer\TempCustomerMobileVerificationController;
use App\Http\Controllers\CustomerPayment\RechargeCardPaymentController;
use App\Http\Controllers\Payment\AamarpayController;
use App\Http\Controllers\Payment\BdsmartpayController;
use App\Http\Controllers\Payment\BkashCheckoutController;
use App\Http\Controllers\Payment\BkashPaymentController;
use App\Http\Controllers\Payment\bKashTokenizedCustomerPaymentController;
use App\Http\Controllers\Payment\EasypaywayTransactionController;
use App\Http\Controllers\Payment\NagadPaymentGatewayController;
use App\Http\Controllers\Payment\RazorpayCustomerPaymentController;
use App\Http\Controllers\Payment\SendMoneyController;
use App\Http\Controllers\Payment\ShurjoPayCustomerPaymentController;
use App\Http\Controllers\Payment\SslcommerzTransactionController;
use App\Http\Controllers\Payment\WalletMixGatewayController;
use App\Http\Controllers\ProfileUpdateByCustomerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/

#start <<web login>>
Route::get('/', [CustomerWebLoginController::class, 'create'])
    ->name('root')
    ->middleware('guestCustomer:customer');

Route::post('customer/web/login', [CustomerWebLoginController::class, 'login'])
    ->middleware('guestCustomer:customer')
    ->name('customer.web.login');

Route::resource('forgot-customer-id', CustomerIdRecoveryController::class)
    ->middleware('guestCustomer:customer')
    ->only(['create', 'store']);
#end <<web login>>

# hotspot login
Route::post('hotspot/login', [HotspotLoginController::class, 'store'])->name('hotspot.login');

#start <<Mobile Verification>>
Route::resource('temp_customers.mobile-verification', TempCustomerMobileVerificationController::class)->only(['create', 'store']);

Route::middleware(['authCustomer:customer'])->group(function () {
    Route::get('customer/mobile/verification', [CustomerMobileVerificationController::class, 'create'])
        ->name('customer.mobile.verification');

    Route::post('customer/mobile/verification', [CustomerMobileVerificationController::class, 'store']);
});
#end <<Mobile Verification>>

#start <<Web UI>>
Route::prefix('customers')->name('customers.')->middleware(['authCustomer:customer'])->group(function () {
    Route::post('web/logout', [CustomerWebLoginController::class, 'logout'])->name('web.logout');
    Route::get('network-collision/{new_network}', [CustomersWebInterfaceController::class, 'networkCollision'])->name('network-collision');
    Route::get('home', [CustomersWebInterfaceController::class, 'home'])->name('home');
    Route::get('profile', [CustomersWebInterfaceController::class, 'profile'])->name('profile');
    Route::get('data-usage-history', [CustomersWebInterfaceController::class, 'radaccts'])->name('radaccts');
    Route::get('graph', [CustomersWebInterfaceController::class, 'graph'])->name('graph');
    Route::get('live_traffic', [CustomersWebInterfaceController::class, 'liveTraffic'])->name('live_traffic');
    Route::get('packages', [CustomersWebInterfaceController::class, 'packages'])->name('packages');
    Route::get('bills', [CustomersWebInterfaceController::class, 'bills'])->name('bills');
    Route::get('payments', [CustomersWebInterfaceController::class, 'payments'])->name('payments');
    Route::get('card-stores', [CustomersWebInterfaceController::class, 'cardStores'])->name('card-stores');
    Route::resource('edit-profile', ProfileUpdateByCustomerController::class)->only(['create', 'store']);
    Route::resource('card-recharge', CardRechargeController::class)->only(['create', 'store']);
    Route::get('package/{package}/purchase', [CustomerPackagePurchaseController::class, 'create'])->name('purchase-package');
    Route::get('pay-bill/{customer_bill}/create', [CustomersPayBillController::class, 'create'])->name('pay-bill');
    Route::resource('replace-mac-address', CustomersMacAddressReplaceController::class)->only(['index', 'create', 'store']);
});
#end <<Web UI>>

# complaints UI
Route::middleware(['authCustomer:customer', 'verifiedMobile'])->group(function () {
    Route::resource('complaints-customer-interface', ComplaintCustomerInterfaceController::class)
        ->except(['edit', 'destroy'])
        ->parameters([
            'complaints-customer-interface' => 'customer_complain'
        ]);
});

#start <<Payment>>
Route::middleware(['authCustomer:customer'])->group(function () {

    //Pay Internet Payment with recharge cards
    Route::resource('customer_payments.recharge-cards', RechargeCardPaymentController::class)
        ->only(['create', 'store']);

    //Pay Internet Payment With Bkash Checkout
    Route::get('bkash/customer_payments/{customer_payment}', [BkashCheckoutController::class, 'initiateCustomerPayment'])
        ->name('bkash.customer_payment.initiate');

    Route::get('bkash/customer_payment/{customer_payment}/create', [BkashCheckoutController::class, 'createCustomerPayment'])
        ->name('bkash.customer_payment.create');

    Route::get('bkash/customer_payment/{customer_payment}/execute', [BkashCheckoutController::class, 'executeCustomerPayment'])
        ->name('bkash.customer_payment.execute');

    Route::get('bkash/customer_payment/{customer_payment}/query', [BkashCheckoutController::class, 'queryCustomerPayment'])
        ->name('bkash.customer_payment.query');

    Route::get('bkash/customer_payment/{customer_payment}/search', [BkashCheckoutController::class, 'searchCustomerTransaction'])
        ->name('bkash.customer_payment.search');

    Route::get('bkash/customer_payment/{customer_payment}/success', [BkashCheckoutController::class, 'successCustomerPayment'])
        ->name('bkash.customer_payment.success');

    //Pay Internet Payment With Send Money
    Route::get('send-money/customer_payments/{customer_payment}', [SendMoneyController::class, 'createCustomerPayment'])
        ->name('send_money.customer_payment.create');

    Route::post('send-money/customer_payment/{customer_payment}/store', [SendMoneyController::class, 'storeCustomerPayment'])
        ->name('send_money.customer_payment.store');

    Route::get('send-money/customer_payment/{customer_payment}/success', [SendMoneyController::class, 'successCustomerPayment'])
        ->name('send_money.customer_payment.success');

    //Pay Internet Payment With bKash Payment (bKash Merchant without API)
    Route::get('bkash_payment/customer_payments/{customer_payment}', [BkashPaymentController::class, 'createCustomerPayment'])
        ->name('bkash_payment.customer_payment.create');

    Route::post('bkash_payment/customer_payment/{customer_payment}/store', [BkashPaymentController::class, 'storeCustomerPayment'])
        ->name('bkash_payment.customer_payment.store');

    Route::get('bkash_payment/customer_payment/{customer_payment}/success', [BkashPaymentController::class, 'successCustomerPayment'])
        ->name('bkash_payment.customer_payment.success');

    // Pay Internet Payment with bKash Tokenized Checkout
    Route::get('bkash_tokenized/customer_payment/{customer_payment}', [bKashTokenizedCustomerPaymentController::class, 'initiatePayment'])
        ->name('bkash_tokenized.customer_payment.initiate');

    Route::get('bkash_tokenized/customer_payment/agreement/create_agreement/{customer_payment}', [bKashTokenizedCustomerPaymentController::class, 'createAgreement'])
        ->name('bkash_tokenized.customer_payment.create_agreement');

    Route::get('bkash_tokenized/customer_payment/agreement/agreement_callback', [bKashTokenizedCustomerPaymentController::class, 'callbackAgreement'])
        ->name('bkash_tokenized.customer_payment.agreement_callback');

    Route::post('bkash_tokenized/customer_payment/agreement/cancel_agreement/{customer_payment}', [bKashTokenizedCustomerPaymentController::class, 'cancelAgreement'])
        ->name('bkash_tokenized.customer_payment.cancel_agreement');

    Route::get('bkash_tokenized/customer_payment/payment/{customer_payment}/create_payment', [bKashTokenizedCustomerPaymentController::class, 'createPayment'])
        ->name('bkash_tokenized.customer_payment.create_payment');

    Route::get('bkash_tokenized/customer_payment/payment/payment_callback', [bKashTokenizedCustomerPaymentController::class, 'callbackPayment'])
        ->name('bkash_tokenized.customer_payment.payment_callback');
});


//Pay Internet Payment With SSL Commerz
Route::get('sslcommerz/customer_payments/{customer_payment}', [SslcommerzTransactionController::class, 'initiateCustomerPayment'])
    ->name('sslcommerz.customer_payment.initiate');

Route::any('sslcommerz/customer_payment/success', [SslcommerzTransactionController::class, 'successCustomerPayment'])
    ->name('sslcommerz.customer_payment.success');

Route::any('sslcommerz/customer_payment/failed', [SslcommerzTransactionController::class, 'failCustomerPayment'])
    ->name('sslcommerz.customer_payment.failed');

Route::any('sslcommerz/customer_payment/canceled', [SslcommerzTransactionController::class, 'cancelCustomerPayment'])
    ->name('sslcommerz.customer_payment.canceled');

//Pay Internet Payment With easypayway
Route::get('easypayway/customer_payments/{customer_payment}', [EasypaywayTransactionController::class, 'initiateCustomerPayment'])
    ->name('easypayway.customer_payment.initiate');

Route::any('easypayway/customer_payment/success', [EasypaywayTransactionController::class, 'successCustomerPayment'])
    ->name('easypayway.customer_payment.success');

Route::any('easypayway/customer_payment/failed', [EasypaywayTransactionController::class, 'failCustomerPayment'])
    ->name('easypayway.customer_payment.failed');

Route::any('easypayway/customer_payment/canceled', [EasypaywayTransactionController::class, 'cancelCustomerPayment'])
    ->name('easypayway.customer_payment.canceled');

//Pay Internet Payment With Nagad
Route::get('nagad/customer_payments/{customer_payment}', [NagadPaymentGatewayController::class, 'initiateCustomerPayment'])
    ->name('nagad.customer_payment.initiate');

Route::get('nagad/customer_payment/callback', [NagadPaymentGatewayController::class, 'customerPaymentCallback'])
    ->name('nagad.customer_payment.callback');

//Pay Internet Payment With walletmix
Route::get('walletmix/customer_payments/{customer_payment}', [WalletMixGatewayController::class, 'initiateCustomerPayment'])
    ->name('walletmix.customer_payment.initiate');

Route::post('walletmix/customer_payment/callback', [WalletMixGatewayController::class, 'customerPaymentCallback'])
    ->name('walletmix.customer_payment.callback');

//Pay Internet Payment With bdsmartpay
Route::get('bdsmartpay/customer_payments/{customer_payment}', [BdsmartpayController::class, 'initiateCustomerPayment'])
    ->name('bdsmartpay.customer_payment.initiate');

Route::get('bdsmartpay/customer_payment/callback', [BdsmartpayController::class, 'customerPaymentCallback'])
    ->name('bdsmartpay.customer_payment.callback');

//Pay Internet Payment With Aamarpay
Route::get('aamarpay/customer_payments/{customer_payment}', [AamarpayController::class, 'initiateCustomerPayment'])
    ->name('aamarpay.customer_payment.initiate');

Route::match(['get', 'post'], 'aamarpay/customer_payment/success', [AamarpayController::class, 'successCustomerPayment'])
    ->name('aamarpay.customer_payment.success');

Route::match(['get', 'post'], 'aamarpay/customer_payment/failed', [AamarpayController::class, 'failCustomerPayment'])
    ->name('aamarpay.customer_payment.failed');

Route::match(['get', 'post'], 'aamarpay/customer_payment/canceled', [AamarpayController::class, 'cancelCustomerPayment'])
    ->name('aamarpay.customer_payment.canceled');

//Pay Internet Payment With ShurjoPay
Route::get('ShurjoPay/customer_payments/{customer_payment}', [ShurjoPayCustomerPaymentController::class, 'createPayment'])
    ->name('ShurjoPay.customer_payment.create');

Route::get('ShurjoPay/customer_payment/callback', [ShurjoPayCustomerPaymentController::class, 'successCallback'])
    ->name('ShurjoPay.customer_payment.callback');

//Pay Internet Payment With Razorpay
Route::get('razorpay/customer_payments/{customer_payment}', [RazorpayCustomerPaymentController::class, 'createOrder'])
    ->name('razorpay.customer_payment.create');

Route::match(['get', 'post'], 'razorpay/customer_payment/callback', [RazorpayCustomerPaymentController::class, 'callback'])
    ->name('razorpay.customer_payment.callback');
#end <<Payment>>
