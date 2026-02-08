<?php

use App\Http\Controllers\BillingProfileController;
use App\Http\Controllers\BillingProfileHelper;
use App\Http\Controllers\BulkCustomerBillPaidController;
use App\Http\Controllers\BulkCustomerBillsManageController;
use App\Http\Controllers\CashPaymentRuntimeInvoiceController;
use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Http\Controllers\CustomerBillController;
use App\Http\Controllers\CustomerBillsSummaryController;
use App\Http\Controllers\CustomerBillsSummaryDownloadController;
use App\Http\Controllers\CustomerPayment\CustomerPaymentController;
use App\Http\Controllers\CustomerPayment\CustomersPendingPaymentController;
use App\Http\Controllers\CustomerPayment\DueNotifyController;
use App\Http\Controllers\CustomerPayment\DueNotifyProfileController;
use App\Http\Controllers\CustomerPaymentDestroyController;
use App\Http\Controllers\CustomerPaymentDownloadController;
use App\Http\Controllers\CustomerPaymentEditController;
use App\Http\Controllers\CustomersInvoiceDownloadController;
use App\Http\Controllers\CustomersOthersPaymentController;
use App\Http\Controllers\DueDateNotificationController;
use App\Http\Controllers\DueDateReminderController;
use App\Http\Controllers\DueDateReminderHelperController;
use App\Http\Controllers\ExpirationNotifierController;
use App\Http\Controllers\FeeListController;
use App\Http\Controllers\GenerateDueDateReminders;
use App\Http\Controllers\Payment\BkashCheckoutController;
use App\Http\Controllers\Payment\bKashTokenizedSmsPaymentController;
use App\Http\Controllers\Payment\bKashTokenizedSubscriptionPaymentController;
use App\Http\Controllers\Payment\CustomersCashPaymentController;
use App\Http\Controllers\Payment\EasypaywayTransactionController;
use App\Http\Controllers\Payment\NagadPaymentGatewayController;
use App\Http\Controllers\Payment\SendMoneyController;
use App\Http\Controllers\Payment\ShurjoPaySmsPaymentController;
use App\Http\Controllers\Payment\SslcommerzTransactionController;
use App\Http\Controllers\PaymentBreakdownSettingController;
use App\Http\Controllers\PaymentGatewayPaymentsViewController;
use App\Http\Controllers\PaymentLinkBroadcastController;
use App\Http\Controllers\SendPaymentLinkController;
use App\Http\Controllers\Sms\AdvanceSmsPaymentController;
use App\Http\Controllers\Sms\SmsBalanceHistoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionBillController;
use App\Http\Controllers\SubscriptionPaymentController;
use App\Http\Controllers\Sms\SmsBillController;
use App\Http\Controllers\Sms\SmsPaymentController;
use App\Http\Controllers\SubscriptionPaymentVerificationController;
use App\Http\Controllers\SubscriptionPolicyController;
use App\Http\Controllers\TempBillingProfileController;
use App\Http\Controllers\VerifyCustomerPaymentsController;
use App\Http\Controllers\VoucherDownloadController;

/*
|--------------------------------------------------------------------------
| Bills and Payments Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', '2FA'])->group(function () {

    # Start << Billing Profiles >>
    Route::resource('billing-profile-helper', BillingProfileHelper::class)
        ->only(['create']);

    Route::resource('temp_billing_profiles', TempBillingProfileController::class)
        ->only(['create', 'store', 'edit', 'update']);

    Route::resource('billing_profiles', BillingProfileController::class)
        ->only(['index', 'edit', 'update', 'destroy']);
    # Stop << Billing Profiles >>

    #start <<Customer's Bills and Payments(For Admin)>>
    Route::get('/create-customer-bill/{customer}', [CustomerBillGenerateController::class, 'create'])
        ->name('customers.customer_bills.create');

    Route::post('/store-customer-bill/{customer}', [CustomerBillGenerateController::class, 'store'])
        ->name('customers.customer_bills.store');

    Route::resource('customer_bills', CustomerBillController::class)
        ->except(['create', 'store'])
        ->middleware(['ECL', 'EAB']);

    Route::resource('customer_bills_summary', CustomerBillsSummaryController::class)
        ->only(['index', 'create']);

    Route::resource('customer_bills_summary_download', CustomerBillsSummaryDownloadController::class)
        ->only(['store']);

    Route::resource('manage-bulk-customer-bills', BulkCustomerBillsManageController::class)
        ->only(['store']);

    Route::resource('bulk_customer_bill_paids', BulkCustomerBillPaidController::class)
        ->only(['create', 'store']);

    Route::get('customer-bills-print/{customer_bill}', [CustomerBillController::class, 'printOrDownload'])
        ->name('customer_bills.print');

    Route::resource('customers-invoice-download', CustomersInvoiceDownloadController::class)
        ->only(['create', 'store']);

    Route::resource('customer_bills.cash-payments', CustomersCashPaymentController::class)
        ->only(['create', 'store'])
        ->middleware(['ECL', 'EAB']);

    Route::resource('customer_bills.runtime-invoice', CashPaymentRuntimeInvoiceController::class)
        ->only(['index']);

    Route::resource('customer_payments', CustomerPaymentController::class)
        ->only(['index', 'show']);

    Route::resource('payment_gateways.customer_payments', PaymentGatewayPaymentsViewController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('customers-pending-payments', CustomersPendingPaymentController::class)
        ->only(['index', 'update'])
        ->parameters([
            'customers-pending-payments' => 'customer_payment'
        ]);

    Route::resource('verify-send-money', VerifyCustomerPaymentsController::class)
        ->only(['index', 'update'])
        ->parameters([
            'verify-send-money' => 'customer_payment'
        ]);

    Route::resource('customer-payments-download', CustomerPaymentDownloadController::class)
        ->only(['create', 'store']);

    Route::resource('customers.others-payments', CustomersOthersPaymentController::class)
        ->only(['create', 'store']);

    Route::resource('customer_payments.edit', CustomerPaymentEditController::class)
        ->only(['create', 'store']);

    Route::resource('customer_payments.destroy', CustomerPaymentDestroyController::class)
        ->only(['create', 'store']);

    Route::resource('customer_payments.voucher', VoucherDownloadController::class)
        ->only(['create']);

    Route::resource('customer-payment-breakdown', PaymentBreakdownSettingController::class)
        ->only(['create', 'store']);
    #end <<Customer's Bills and Payments (For Admin)>>

    #<<due-notifier
    Route::resource('due-notifier', DueNotifyProfileController::class)
        ->only(['create', 'store']);

    Route::resource('due-date.due-notifier', DueNotifyController::class)
        ->only(['create', 'store']);
    // old >> new
    Route::resource('due_date_reminders_helper', DueDateReminderHelperController::class)
        ->only(['index']);

    Route::resource('due_date_reminders', DueDateReminderController::class)->except(['show']);

    Route::resource('due_date_reminders.notification', DueDateNotificationController::class)
        ->only(['create', 'store']);

    Route::resource('generate-due-date-reminders', GenerateDueDateReminders::class)
        ->only(['create', 'store']);

    Route::resource('expiration_notifiers', ExpirationNotifierController::class)
        ->only(['index', 'edit', 'update']);
    #due-notifier>>

    #<<payment link
    Route::resource('payment-link-broadcast', PaymentLinkBroadcastController::class)
        ->only(['create', 'store', 'edit'])
        ->parameters([
            'payment-link-broadcast' => 'billing_profile'
        ]);
    Route::resource('customer.send-payment-link', SendPaymentLinkController::class)
        ->only(['create', 'store']);
    #payment link>>

    #start <<SMS Bills and Payments>>
    Route::resource('sms_bills', SmsBillController::class)
        ->only(['index', 'edit', 'update', 'destroy']);

    Route::resource('sms_payments', SmsPaymentController::class)
        ->only(['index']);

    Route::get('sms_payments/{sms_payment}/recheck', [SmsPaymentController::class, 'recheckPayment'])
        ->name('sms_payments.recheck');

    Route::resource('advance_sms_payments', AdvanceSmsPaymentController::class)
        ->only(['create', 'store']);

    Route::resource('sms_balance_histories', SmsBalanceHistoryController::class)
        ->only(['index']);
    #end <<SMS Bills and Payments>>

    #start <<Subscription Bills and Payments>>
    Route::get('pricing', [FeeListController::class, 'index'])
        ->name('software.pricing');

    Route::resource('subscription_policies', SubscriptionPolicyController::class)
        ->only(['index']);

    Route::resource('subscription_bills', SubscriptionBillController::class)
        ->only(['index', 'edit', 'update', 'destroy']);

    Route::resource('subscription_payments', SubscriptionPaymentController::class)
        ->only(['index']);

    Route::get('subscription_payments/{subscription_payment}/recheck', [SubscriptionPaymentController::class, 'recheckPayment'])
        ->name('subscription_payments.recheck');

    Route::resource('verify-subscription-payment', SubscriptionPaymentVerificationController::class)
        ->only(['create']);
    #end <<Subscription Bills and Payments>>
});



/*
|--------------------------------------------------------------------------
| SMS Payment Group || Exclude From Redirect to Pay SMS Payment
|--------------------------------------------------------------------------
*/

Route::get('sms_payments/{sms_bill}/create', [SmsPaymentController::class, 'create'])->name('sms_payments.create')
    ->middleware('auth');

//Pay SMS Payment With Bkash Checkout
Route::middleware(['auth'])->group(function () {

    Route::get('bkash/sms_payments/{sms_payment}', [BkashCheckoutController::class, 'initiateSmsPayment'])
        ->name('bkash.sms_payment.initiate');

    Route::get('bkash/sms_payment/{sms_payment}/create', [BkashCheckoutController::class, 'createSmsPayment'])
        ->name('bkash.sms_payment.create');

    Route::get('bkash/sms_payment/{sms_payment}/execute', [BkashCheckoutController::class, 'executeSmsPayment'])
        ->name('bkash.sms_payment.execute');

    Route::get('bkash/sms_payment/{sms_payment}/query', [BkashCheckoutController::class, 'querySmsPayment'])
        ->name('bkash.sms_payment.query');

    Route::get('bkash/sms_payment/{sms_payment}/search', [BkashCheckoutController::class, 'searchSmsTransaction'])
        ->name('bkash.sms_payment.search');

    Route::get('bkash/sms_payment/{sms_payment}/success', [BkashCheckoutController::class, 'successSmsPayment'])
        ->name('bkash.sms_payment.success');
});

//Pay SMS Payment With Bkash Tokenized Checkout
Route::middleware(['auth'])->group(function () {

    Route::get('bkash_tokenized/sms_payment/{sms_payment}', [bKashTokenizedSmsPaymentController::class, 'initiatePayment'])
        ->name('bkash_tokenized.sms_payment.initiate');

    Route::get('bkash_tokenized/sms_payment/agreement/create_agreement/{sms_payment}', [bKashTokenizedSmsPaymentController::class, 'createAgreement'])
        ->name('bkash_tokenized.sms_payment.create_agreement');

    Route::get('bkash_tokenized/sms_payment/agreement/agreement_callback', [bKashTokenizedSmsPaymentController::class, 'callbackAgreement'])
        ->name('bkash_tokenized.sms_payment.agreement_callback');

    Route::post('bkash_tokenized/sms_payment/agreement/cancel_agreement/{sms_payment}', [bKashTokenizedSmsPaymentController::class, 'cancelAgreement'])
        ->name('bkash_tokenized.sms_payment.cancel_agreement');

    Route::get('bkash_tokenized/sms_payment/payment/{sms_payment}/create_payment', [bKashTokenizedSmsPaymentController::class, 'createPayment'])
        ->name('bkash_tokenized.sms_payment.create_payment');

    Route::get('bkash_tokenized/sms_payment/payment/payment_callback', [bKashTokenizedSmsPaymentController::class, 'callbackPayment'])
        ->name('bkash_tokenized.sms_payment.payment_callback');
});

//Pay SMS Payment With SSL Commerz
Route::get('sslcommerz/sms_payments/{sms_payment}', [SslcommerzTransactionController::class, 'initiateSmsPayment'])
    ->name('sslcommerz.sms_payment.initiate')
    ->middleware('auth');

Route::any('sslcommerz/sms_payment/success', [SslcommerzTransactionController::class, 'successSmsPayment'])
    ->name('sslcommerz.sms_payment.success');

Route::any('sslcommerz/sms_payment/failed', [SslcommerzTransactionController::class, 'failSmsPayment'])
    ->name('sslcommerz.sms_payment.failed');

Route::any('sslcommerz/sms_payment/canceled', [SslcommerzTransactionController::class, 'cancelSmsPayment'])
    ->name('sslcommerz.sms_payment.canceled');

//Pay SMS Payment With easypayway
Route::get('easypayway/sms_payments/{sms_payment}', [EasypaywayTransactionController::class, 'initiateSmsPayment'])
    ->name('easypayway.sms_payment.initiate')
    ->middleware('auth');

Route::any('easypayway/sms_payment/success', [EasypaywayTransactionController::class, 'successSmsPayment'])
    ->name('easypayway.sms_payment.success');

Route::any('easypayway/sms_payment/failed', [EasypaywayTransactionController::class, 'failSmsPayment'])
    ->name('easypayway.sms_payment.failed');

Route::any('easypayway/sms_payment/canceled', [EasypaywayTransactionController::class, 'cancelSmsPayment'])
    ->name('easypayway.sms_payment.canceled');

//Pay SMS Payment With Nagad
Route::get('nagad/sms_payments/{sms_payment}', [NagadPaymentGatewayController::class, 'initiateSmsPayment'])
    ->name('nagad.sms_payment.initiate')
    ->middleware('auth');

Route::get('nagad/sms_payment/callback', [NagadPaymentGatewayController::class, 'smsPaymentCallback'])
    ->name('nagad.sms_payment.callback');


//Pay SMS Payment With ShurjoPay
Route::get('shurjopay/sms_payments/{sms_payment}', [ShurjoPaySmsPaymentController::class, 'createPayment'])
    ->name('shurjopay.sms_payment.create')
    ->middleware('auth');

Route::get('shurjopay/sms_payment/callback', [ShurjoPaySmsPaymentController::class, 'successCallback'])
    ->name('shurjopay.sms_payment.callback');


/*
|--------------------------------------------------------------------------------
| Subscription Payment Group || Exclude From Redirect to Pay Subscription Payment
|--------------------------------------------------------------------------------
*/

Route::get('subscription_payments/{subscription_bill}/create', [SubscriptionPaymentController::class, 'create'])
    ->name('subscription_payments.create')
    ->middleware('auth');

//Pay Subscription Payment With Bkash Checkout
Route::middleware(['auth'])->group(function () {
    Route::get('bkash/subscription_payments/{subscription_payment}', [BkashCheckoutController::class, 'initiateSubscriptionPayment'])
        ->name('bkash.subscription_payment.initiate');

    Route::get('bkash/subscription_payment/{subscription_payment}/create', [BkashCheckoutController::class, 'createSubscriptionPayment'])
        ->name('bkash.subscription_payment.create');

    Route::get('bkash/subscription_payment/{subscription_payment}/execute', [BkashCheckoutController::class, 'executeSubscriptionPayment'])
        ->name('bkash.subscription_payment.execute');

    Route::get('bkash/subscription_payment/{subscription_payment}/query', [BkashCheckoutController::class, 'querySubscriptionPayment'])
        ->name('bkash.subscription_payment.query');

    Route::get('bkash/subscription_payment/{subscription_payment}/search', [BkashCheckoutController::class, 'searchSubscriptionTransaction'])
        ->name('bkash.subscription_payment.search');

    Route::get('bkash/subscription_payment/{subscription_payment}/success', [BkashCheckoutController::class, 'successSubscriptionPayment'])
        ->name('bkash.subscription_payment.success');
});

//Pay Subscription Payment With Bkash Tokenized Checkout
Route::middleware(['auth'])->group(function () {

    Route::get('bkash_tokenized/subscription_payment/{subscription_payment}', [bKashTokenizedSubscriptionPaymentController::class, 'initiatePayment'])
        ->name('bkash_tokenized.subscription_payment.initiate');

    Route::get('bkash_tokenized/subscription_payment/agreement/create_agreement/{subscription_payment}', [bKashTokenizedSubscriptionPaymentController::class, 'createAgreement'])
        ->name('bkash_tokenized.subscription_payment.create_agreement');

    Route::get('bkash_tokenized/subscription_payment/agreement/agreement_callback', [bKashTokenizedSubscriptionPaymentController::class, 'callbackAgreement'])
        ->name('bkash_tokenized.subscription_payment.agreement_callback');

    Route::post('bkash_tokenized/subscription_payment/agreement/cancel_agreement/{subscription_payment}', [bKashTokenizedSubscriptionPaymentController::class, 'cancelAgreement'])
        ->name('bkash_tokenized.subscription_payment.cancel_agreement');

    Route::get('bkash_tokenized/subscription_payment/payment/{subscription_payment}/create_payment', [bKashTokenizedSubscriptionPaymentController::class, 'createPayment'])
        ->name('bkash_tokenized.subscription_payment.create_payment');

    Route::get('bkash_tokenized/subscription_payment/payment/payment_callback', [bKashTokenizedSubscriptionPaymentController::class, 'callbackPayment'])
        ->name('bkash_tokenized.subscription_payment.payment_callback');
});

//Pay Subscription Payment With SSL Commerz
Route::get('sslcommerz/subscription_payments/{subscription_payment}', [SslcommerzTransactionController::class, 'initiateSubscriptionPayment'])
    ->name('sslcommerz.subscription_payment.initiate')
    ->middleware('auth');

Route::any('sslcommerz/subscription_payment/success', [SslcommerzTransactionController::class, 'successSubscriptionPayment'])
    ->name('sslcommerz.subscription_payment.success');

Route::any('sslcommerz/subscription_payment/failed', [SslcommerzTransactionController::class, 'failSubscriptionPayment'])
    ->name('sslcommerz.subscription_payment.failed');

Route::any('sslcommerz/subscription_payment/canceled', [SslcommerzTransactionController::class, 'cancelSubscriptionPayment'])
    ->name('sslcommerz.subscription_payment.canceled');

//Pay Subscription Payment With easypayway
Route::get('easypayway/subscription_payments/{subscription_payment}', [EasypaywayTransactionController::class, 'initiateSubscriptionPayment'])
    ->name('easypayway.subscription_payment.initiate')
    ->middleware('auth');

Route::any('easypayway/subscription_payment/success', [EasypaywayTransactionController::class, 'successSubscriptionPayment'])
    ->name('easypayway.subscription_payment.success');

Route::any('easypayway/subscription_payment/failed', [EasypaywayTransactionController::class, 'failSubscriptionPayment'])
    ->name('easypayway.subscription_payment.failed');

Route::any('easypayway/subscription_payment/canceled', [EasypaywayTransactionController::class, 'cancelSubscriptionPayment'])
    ->name('easypayway.subscription_payment.canceled');


//Pay Subscription Payment With Nagad
Route::get('nagad/subscription_payments/{subscription_payment}', [NagadPaymentGatewayController::class, 'initiateSubscriptionPayment'])
    ->name('nagad.subscription_payment.initiate')
    ->middleware('auth');

Route::get('nagad/subscription_payment/callback', [NagadPaymentGatewayController::class, 'subscriptionPaymentCallback'])
    ->name('nagad.subscription_payment.callback');

//Pay Subscription Payment With Send Money
Route::get('send-money/subscription_payments/{subscription_payment}', [SendMoneyController::class, 'createSubscriptionPayment'])
    ->name('send_money.subscription_payment.create')
    ->middleware('auth');

Route::post('send-money/subscription_payment/{subscription_payment}/store', [SendMoneyController::class, 'storeSubscriptionPayment'])
    ->name('send_money.subscription_payment.store')
    ->middleware('auth');
