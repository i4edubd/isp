<?php

use App\Http\Controllers\AccountBalanceAddController;
use App\Http\Controllers\ActivateFupController;
use App\Http\Controllers\ActiveCustomerWidgetController;
use App\Http\Controllers\AffiliateLeadsController;
use App\Http\Controllers\AffiliateLinkController;
use App\Http\Controllers\AmountDueWidgetController;
use App\Http\Controllers\AmountPaidWidgetController;
use App\Http\Controllers\ArchivedCustomerComplainController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupAdminController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseSubcategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\RechargeCardController;
use App\Http\Controllers\Freeradius\NasController;
use App\Http\Controllers\BackupSettingController;
use App\Http\Controllers\BilledCustomerWidgetController;
use App\Http\Controllers\BillingProfileReplaceController;
use App\Http\Controllers\BillsVsPaymentsChartController;
use App\Http\Controllers\BTRCReportController;
use App\Http\Controllers\BulkMacBindController;
use App\Http\Controllers\BulkUpdateUsersController;
use App\Http\Controllers\CardDistributorController;
use App\Http\Controllers\CardDistributorPaymentsController;
use App\Http\Controllers\CardDistributorsPaymentsDownloadController;
use App\Http\Controllers\ChildCustomerAccountController;
use App\Http\Controllers\ComplainAcknowledgeController;
use App\Http\Controllers\ComplainCategoryController;
use App\Http\Controllers\ComplainCategoryEditController;
use App\Http\Controllers\ComplainCommentController;
use App\Http\Controllers\ComplainDepartmentController;
use App\Http\Controllers\ComplaintReportController;
use App\Http\Controllers\ComplaintStatisticsChartController;
use App\Http\Controllers\CreditLimitEditController;
use App\Http\Controllers\Customer\CustomerActivateController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\CustomerCreateController;
use App\Http\Controllers\Customer\CustomerCustomAttributeController;
use App\Http\Controllers\Customer\CustomerDetailsController;
use App\Http\Controllers\Customer\CustomerDisableController;
use App\Http\Controllers\Customer\CustomerDuplicateValueCheckController;
use App\Http\Controllers\Customer\CustomerMacBindController;
use App\Http\Controllers\Customer\CustomerMobileSearchController;
use App\Http\Controllers\Customer\CustomerNameSearchController;
use App\Http\Controllers\Customer\CustomerPackageChangeController;
use App\Http\Controllers\Customer\CustomerSpeedLimitController;
use App\Http\Controllers\Customer\CustomersSmsHistoryCreateController;
use App\Http\Controllers\Customer\CustomerSuspendController;
use App\Http\Controllers\Customer\CustomerTimeLimitController;
use App\Http\Controllers\Customer\CustomerUsernameSearchController;
use App\Http\Controllers\Customer\CustomerVolumeLimitController;
use App\Http\Controllers\Customer\OnlineCustomersController;
use App\Http\Controllers\Customer\PPPoECustomersImportController;
use App\Http\Controllers\Customer\TempCustomerController;
use App\Http\Controllers\CustomerActivateOptionController;
use App\Http\Controllers\CustomerAdvancePaymentController;
use App\Http\Controllers\CustomerBackupRequestController;
use App\Http\Controllers\CustomerBillController;
use App\Http\Controllers\CustomerBillingProfileEditController;
use App\Http\Controllers\CustomerComplainController;
use App\Http\Controllers\CustomerIdSearchController;
use App\Http\Controllers\CustomerIpEditController;
use App\Http\Controllers\CustomerStatisticsChartController;
use App\Http\Controllers\CustomerZoneController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\CustomPriceController;
use App\Http\Controllers\DailyBillingPackageChangeController;
use App\Http\Controllers\DataPolicyController;
use App\Http\Controllers\DeletedCustomerController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeveloperNoticeBroadcastController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DisabledCustomerWidgetController;
use App\Http\Controllers\DisabledFilterController;
use App\Http\Controllers\DisabledMenuController;
use App\Http\Controllers\DownloadusersDownloadableController;
use App\Http\Controllers\DownloadusersUploadableController;
use App\Http\Controllers\EventSmsController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExtendPackageValidityController;
use App\Http\Controllers\FailedLoginViewController;
use App\Http\Controllers\FairUsagePolicyController;
use App\Http\Controllers\ForeignRouterController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\GeneralComplaintController;
use App\Http\Controllers\GlobalCustomerSearchController;
use App\Http\Controllers\HotspotPackageChangeController;
use App\Http\Controllers\HotspotRechargeController;
use App\Http\Controllers\IncomeVsExpenseController;
use App\Http\Controllers\InternetHistoryDownloadController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\Ipv4poolController;
use App\Http\Controllers\Ipv4poolNameController;
use App\Http\Controllers\Ipv4poolReplaceController;
use App\Http\Controllers\Ipv4poolSubnetController;
use App\Http\Controllers\Ipv6poolController;
use App\Http\Controllers\Ipv6poolNameController;
use App\Http\Controllers\Ipv6poolReplaceController;
use App\Http\Controllers\Ipv6poolSubnetController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MandatoryCustomersAttributeController;
use App\Http\Controllers\MasterPackageController;
use App\Http\Controllers\MaxSubscriptionPaymentController;
use App\Http\Controllers\MinimumConfigurationController;
use App\Http\Controllers\MinimumSmsBillController;
use App\Http\Controllers\MultipleCustomerEditController;
use App\Http\Controllers\NasPppoeProfileController;
use App\Http\Controllers\NewRegistrationWidgetController;
use App\Http\Controllers\OfflineCustomerController;
use App\Http\Controllers\OnlineCustomerWidgetController;
use App\Http\Controllers\OperatorActivateController;
use App\Http\Controllers\OperatorBillingProfileController;
use App\Http\Controllers\OperatorChangeController;
use App\Http\Controllers\OperatorDeleteController;
use App\Http\Controllers\OperatorDestroyController;
use App\Http\Controllers\OperatorMasterPackageController;
use App\Http\Controllers\OperatorPackageController;
use App\Http\Controllers\OperatorProfileEditController;
use App\Http\Controllers\OperatorsIncomeController;
use App\Http\Controllers\OperatorsIncomeSummaryController;
use App\Http\Controllers\OperatorsNoticeBroadcastController;
use App\Http\Controllers\OperatorsSpecialPermissionController;
use App\Http\Controllers\OperatorSuspendController;
use App\Http\Controllers\MasterPackageCreateController;
use App\Http\Controllers\mpdfTestController;
use App\Http\Controllers\NasNetWatchController;
use App\Http\Controllers\packagePppoeProfilesController;
use App\Http\Controllers\PackageReplaceController;
use App\Http\Controllers\PaidCustomerWidgetController;
use App\Http\Controllers\PgsqlActivityLogController;
use App\Http\Controllers\PingTestController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Http\Controllers\PppDailyRechargeController;
use App\Http\Controllers\PppoeInterfaceTrafficMonitorController;
use App\Http\Controllers\PppoeProfileController;
use App\Http\Controllers\PPPoeProfileIpAllocationModeController;
use App\Http\Controllers\PPPoeProfileIPv4poolController;
use App\Http\Controllers\PPPoeProfileIPv6poolController;
use App\Http\Controllers\PPPoeProfileNameController;
use App\Http\Controllers\PPPoeProfilePackagesController;
use App\Http\Controllers\PPPoeProfileReplaceController;
use App\Http\Controllers\PPPoEProfileUploadCreateController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionExplanationController;
use App\Http\Controllers\QuestionOptionController;
use App\Http\Controllers\RechargeCardDownloadController;
use App\Http\Controllers\RouterConfigurationController;
use App\Http\Controllers\RoutersLogViewerController;
use App\Http\Controllers\SalesCommentController;
use App\Http\Controllers\ScreenShotController;
use App\Http\Controllers\SelfDeletionController;
use App\Http\Controllers\SelfProvisioningController;
use App\Http\Controllers\SelfRegisteredAdminsController;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\Sms\SmsHistoryController;
use App\Http\Controllers\SmsBroadcastJobController;
use App\Http\Controllers\SMSWidgetController;
use App\Http\Controllers\SoftwareDemoController;
use App\Http\Controllers\SubOperatorAccountBalanceAddController;
use App\Http\Controllers\SubOperatorBillingProfileController;
use App\Http\Controllers\SubOperatorController;
use App\Http\Controllers\SubOperatorCreditLimitEditController;
use App\Http\Controllers\SubOperatorDeleteController;
use App\Http\Controllers\SubscriptionBillController;
use App\Http\Controllers\SubscriptionBillPaidController;
use App\Http\Controllers\SubscriptionDiscountController;
use App\Http\Controllers\SubscriptionPaymentReportController;
use App\Http\Controllers\SupportProgrammePolicyController;
use App\Http\Controllers\SupportProgrammeSalesController;
use App\Http\Controllers\SuspendCustomerWidgetController;
use App\Http\Controllers\SuspendDateEditController;
use App\Http\Controllers\TempCustomerBillInfoController;
use App\Http\Controllers\TempCustomerBillingProfileController;
use App\Http\Controllers\TempCustomerTechInfoController;
use App\Http\Controllers\TempPackageController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TodaysUpdateWidgetController;
use App\Http\Controllers\TotalCustomerWidgetController;
use App\Http\Controllers\VariableNameController;
use App\Http\Controllers\VatCollectionController;
use App\Http\Controllers\VatProfileController;
use App\Http\Controllers\VpnAccountController;
use App\Http\Controllers\VpnPoolController;
use App\Http\Controllers\WalledGardenController;
use App\Http\Controllers\YearlyCardDistributorPaymentController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Freeradius\RadreplyController;
use App\Http\Controllers\OperatorSubOperatorPaymentsController;
use App\Http\Controllers\Customer\PPPoEImportFromXLController;
use App\Http\Controllers\OperatorsOnlinePaymentController;
use App\Http\Controllers\OperatorPaymentStatementController;

/*
|--------------------------------------------------------------------------
| TEST
|--------------------------------------------------------------------------
*/

// Route::get('/test', [TestController::class, 'e164']);
// Route::get('/testmdpf', mpdfTestController::class);


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('refund-policy', function () {
    return view('laraview.layouts.refund-delivery-policy');
})->name('refund-policy');

Route::get('privacy-policy', function () {
    return view('laraview.layouts.privacy-policy');
})->name('privacy-policy');


/*
|--------------------------------------------------------------------------
| DEMO
|--------------------------------------------------------------------------
*/

Route::resource('demo', SoftwareDemoController::class)
    ->only(['index']);


/*
|--------------------------------------------------------------------------
| Self Service
|--------------------------------------------------------------------------
*/
Route::resource('operators.self-provisioning', SelfProvisioningController::class)
    ->only(['create']);

Route::resource('operators.self-deletion', SelfDeletionController::class)
    ->only(['create']);


/*
|--------------------------------------------------------------------------
| Dashboard || Admin Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verified', '2FA', 'payment.sms', 'payment.subscription', 'pending.transaction'])
    ->name('dashboard');



/*
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'verified', '2FA', 'payment.sms', 'payment.subscription', 'pending.transaction', 'ECL', 'EAB'])->group(function () {

    // $lang = Auth::user()->lang;

    // App::setLocale($lang);

    Route::get('/', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/dashboard/chart', [DashboardController::class, 'dashboardChart'])->name('admin.dashboard.chart');
});


/*
|--------------------------------------------------------------------------
| Dashboard Widgets
|--------------------------------------------------------------------------
*/
Route::prefix('widgets')->middleware(['auth'])->group(function () {

    Route::resource('bills_vs_payments_chart', BillsVsPaymentsChartController::class)
        ->only(['index']);

    Route::resource('customer_statistics_chart', CustomerStatisticsChartController::class)
        ->only(['index']);

    Route::get('/dashboard-customer-bills', [CustomerBillController::class, 'dashboardIndex'])
        ->name('dashboard.customer-bills');

    Route::get('/dashboard-subscription_bill', [SubscriptionBillController::class, 'dashboardIndex'])
        ->name('dashboard.subscription_bills');

    Route::get('/new-customers', [CustomerController::class, 'newCustomers'])
        ->name('dashboard.newCustomers');

    Route::get('/will_be_suspended', [TodaysUpdateWidgetController::class, 'willBeSuspended'])
        ->name('will_be_suspended.index');

    Route::get('/amount_to_be_collected', [TodaysUpdateWidgetController::class, 'amountToBeCollected'])
        ->name('amount_to_be_collected.index');

    Route::get('/collected_amount', [TodaysUpdateWidgetController::class, 'collectedAmount'])
        ->name('collected_amount.index');

    Route::get('/today_sms_sent', [TodaysUpdateWidgetController::class, 'smsSent'])
        ->name('today_sms_sent');

    Route::resource('active_customer_widget', ActiveCustomerWidgetController::class)
        ->only(['index']);

    Route::resource('suspend_customer_widget', SuspendCustomerWidgetController::class)
        ->only(['index']);

    Route::resource('disabled_customer_widget', DisabledCustomerWidgetController::class)
        ->only(['index']);

    Route::resource('billed_customer_widget', BilledCustomerWidgetController::class)
        ->only(['index']);

    Route::resource('paid_customer_widget', PaidCustomerWidgetController::class)
        ->only(['index']);

    Route::resource('total_customer_widget', TotalCustomerWidgetController::class)
        ->only(['index']);

    Route::resource('new_registration_widget', NewRegistrationWidgetController::class)
        ->only(['index']);

    Route::resource('amount_paid_widget', AmountPaidWidgetController::class)
        ->only(['index']);

    Route::resource('amount_due_widget', AmountDueWidgetController::class)
        ->only(['index']);

    Route::resource('sms_widget', SMSWidgetController::class)
        ->only(['index']);

    Route::resource('online_customer_widget', OnlineCustomerWidgetController::class)
        ->only(['index']);

    Route::resource('complaint_statistics_chart', ComplaintStatisticsChartController::class)
        ->only(['index']);
});




/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', '2FA', 'can:accessSuperAdminPanel'])->group(function () {

    Route::resource('group_admins', GroupAdminController::class)
        ->except(['destroy']);

    Route::resource('subscription_bill.paid', SubscriptionBillPaidController::class)
        ->only(['create', 'store']);

    Route::get('/activate-subscription/{operator}', [GroupAdminController::class, 'activateSubscription'])
        ->name('subscription.activate');

    Route::get('/suspend-subscription/{operator}', [GroupAdminController::class, 'suspendSubscription'])
        ->name('subscription.suspend');

    Route::resource('subscription-payment-report', SubscriptionPaymentReportController::class)
        ->only(['create', 'store']);
});


/*
|--------------------------------------------------------------------------
| Group Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', '2FA', 'can:accessGroupAdminPanel', 'payment.subscription'])->group(function () {

    // << helpline
    Route::get('/helpline', function () {
        return view('admins.group_admin.helpline');
    })->name('helpline');

    Route::get('/video-tutorials', function () {
        return view('admins.group_admin.video-tutorials');
    })->name('video-tutorials');
    // helpline >>

    // << operators
    Route::resource('operators', OperatorController::class)
        ->except(['destroy']);

    Route::resource('operators.destroy', OperatorDestroyController::class)
        ->only(['create', 'store'])
        ->middleware('password.confirm');

    Route::resource('operators.suspend', OperatorSuspendController::class)
        ->only(['create', 'store']);

    Route::resource('operators.activate', OperatorActivateController::class)
        ->only(['store']);

    Route::resource('operators.credit-limit', CreditLimitEditController::class)
        ->only(['create', 'store']);

    Route::resource('operators.account-balance', AccountBalanceAddController::class)
        ->only(['create', 'store']);

    Route::resource('operators.billing_profiles', OperatorBillingProfileController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('operators.special-permission', OperatorsSpecialPermissionController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('operators-notice-broadcast', OperatorsNoticeBroadcastController::class)
        ->only(['create', 'store']);
    // operators>>

    // <<Router
    Route::resource('vpn_accounts', VpnAccountController::class)
        ->except(['edit', 'update']);

    Route::resource('routers', NasController::class);

    Route::resource('routers.configuration', RouterConfigurationController::class)
        ->only(['create', 'store']);

    Route::resource('routers.walled-garden', WalledGardenController::class)
        ->only(['create', 'store']);

    Route::resource('routers.netwatch', NasNetWatchController::class)
        ->only(['create', 'store']);

    Route::resource('routers.logs', RoutersLogViewerController::class)
        ->only(['index']);
    // Router>>

    // <<ipv4pool
    Route::get('/ipv4pools/check/duplicate/name/{name}', [Ipv4poolController::class, 'checkDuplicateName']);

    Route::get('/ipv4pools/check/duplicate/subnet', [Ipv4poolController::class, 'checkSubnetError']);

    Route::resource('ipv4pools', Ipv4poolController::class)
        ->except(['show', 'edit', 'update']);

    Route::resource('ipv4pool_name', Ipv4poolNameController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'ipv4pool_name' => 'ipv4pool'
        ]);

    Route::get('/ipv4pool_subnet/check/{ipv4pool}/error', [Ipv4poolSubnetController::class, 'checkError']);

    Route::resource('ipv4pool_subnet', Ipv4poolSubnetController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'ipv4pool_subnet' => 'ipv4pool'
        ]);

    Route::resource('ipv4pool_replace', Ipv4poolReplaceController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'ipv4pool_replace' => 'ipv4pool'
        ]);
    // ipv4pool>>

    // <<ipv6pool
    Route::get('/ipv6pools/check/duplicate/name/{name}', [Ipv6poolController::class, 'checkDuplicateName']);

    Route::get('/ipv6pools/check/duplicate/prefix', [Ipv6poolController::class, 'checkPrefixError']);

    Route::resource('ipv6pools', Ipv6poolController::class)
        ->except(['show', 'edit', 'update']);

    Route::resource('ipv6pool_name', Ipv6poolNameController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'ipv6pool_name' => 'ipv6pool'
        ]);

    Route::get('/ipv6pool_subnet/check/{ipv6pool}/error', [Ipv6poolSubnetController::class, 'checkError']);

    Route::resource('ipv6pool_subnet', Ipv6poolSubnetController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'ipv6pool_subnet' => 'ipv6pool'
        ]);

    Route::resource('ipv6pool_replace', Ipv6poolReplaceController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'ipv6pool_replace' => 'ipv6pool'
        ]);
    // ipv6pool>>

    // <<< ppp profile
    Route::resource('pppoe_profiles', PppoeProfileController::class)
        ->except(['show', 'edit', 'update']);

    Route::resource('pppoe_profile_name', PPPoeProfileNameController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'pppoe_profile_name' => 'pppoe_profile'
        ]);

    Route::resource('pppoe_profile_ipv4pool', PPPoeProfileIPv4poolController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'pppoe_profile_ipv4pool' => 'pppoe_profile'
        ]);

    Route::resource('pppoe_profile_ipv6pool', PPPoeProfileIPv6poolController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'pppoe_profile_ipv6pool' => 'pppoe_profile'
        ]);

    Route::resource('pppoe_profile_ip_allocation_mode', PPPoeProfileIpAllocationModeController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'pppoe_profile_ip_allocation_mode' => 'pppoe_profile'
        ]);

    Route::resource('pppoe_profile_replace', PPPoeProfileReplaceController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'pppoe_profile_replace' => 'pppoe_profile'
        ]);

    Route::resource('pppoe_profiles.master_packages', PPPoeProfilePackagesController::class)
        ->only(['index']);

    Route::resource('routers.pppoe_profiles', NasPppoeProfileController::class)
        ->only(['index', 'create', 'store']);
    // ppp profile>>

    Route::resource('billing_profile_replace', BillingProfileReplaceController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'billing_profile_replace' => 'billing_profile'
        ]);

    // <<master packages
    Route::resource('temp_packages', TempPackageController::class)
        ->only(['store', 'edit', 'update']);

    Route::resource('temp_packages.master_packages', MasterPackageCreateController::class)
        ->only(['create', 'store']);

    Route::resource('master_packages', MasterPackageController::class)
        ->only(['index', 'edit', 'update', 'destroy']);

    Route::resource('master_packages.pppoe_profiles', packagePppoeProfilesController::class)
        ->only(['edit', 'update']);

    Route::resource('master_packages.fair_usage_policy', FairUsagePolicyController::class)
        ->except(['show']);

    Route::resource('operators.master_packages', OperatorMasterPackageController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);
    // master packages>>

    Route::resource('backup_settings', BackupSettingController::class)
        ->except(['show']);

    Route::resource('backup_settings.customer_backup_request', CustomerBackupRequestController::class)
        ->only(['create']);

    Route::get('/upload-ppp-profiles', [PPPoEProfileUploadCreateController::class, '__invoke'])
        ->name('upload-ppp-profile');

    Route::post('/upload-ppp-profiles', [PPPoEProfileUploadCreateController::class, 'create']);

    Route::resource('btrc-report', BTRCReportController::class)
        ->only(['create', 'store']);

    Route::resource('mandatory_customers_attributes', MandatoryCustomersAttributeController::class)
        ->only(['index', 'store']);

    // Remove any duplicate definitions of operator_suboperator_payments.index
    Route::get('/operator-suboperator-payments', [OperatorSubOperatorPaymentsController::class, 'index'])
        ->name('operator_suboperator_payments.index');

    Route::get('/operator-suboperator-payments-download', [OperatorSubOperatorPaymentsController::class, 'download'])
        ->name('operator_suboperator_payments_download.create');
        
    Route::prefix('mikrotik')->group(function () {
    Route::get('interfaces', [RadreplyController::class, 'viewInterfaces'])->name('mikrotik.interfaces');
    Route::post('add-ip', [RadreplyController::class, 'addIp'])->name('mikrotik.add_ip');
    Route::post('edit-ip/{id}', [RadreplyController::class, 'editIp'])->name('mikrotik.edit_ip');
    Route::delete('delete-ip/{id}', [RadreplyController::class, 'deleteIp'])->name('mikrotik.delete_ip');
    Route::post('add-vlan', [RadreplyController::class, 'addVlan'])->name('mikrotik.add_vlan');
    Route::get('vlans', [RadreplyController::class, 'viewVlans'])->name('mikrotik.vlans');
    Route::delete('delete-vlan/{id}', [RadreplyController::class, 'deleteVlan'])->name('mikrotik.delete_vlan');
    Route::get('arp', [RadreplyController::class, 'viewArp'])->name('mikrotik.arp');
    Route::get('export-config', [RadreplyController::class, 'exportConfig'])->name('mikrotik.export_config');
    Route::get('bridges', [RadreplyController::class, 'viewBridges'])->name('mikrotik.bridges');
    Route::post('add-bridge', [RadreplyController::class, 'addBridge'])->name('mikrotik.add_bridge');
    Route::get('hosts', [RadreplyController::class, 'viewHosts'])->name('mikrotik.hosts');
    Route::get('traffic', [RadreplyController::class, 'viewTraffic'])->name('mikrotik.traffic');
    });
});



/*
|--------------------------------------------------------------------------
| General Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    Route::resource('disabled_filters', DisabledFilterController::class)
        ->only(['create', 'store']);

    Route::resource('disabled_menus', DisabledMenuController::class)
        ->only(['create', 'store']);

    Route::get('/operator-select-option', [FormController::class, 'selectOperator']);

    Route::get('/options-for-account-type', [FormController::class, 'optionsforAccountType']);

    Route::get('/variable-name', [VariableNameController::class, '__invoke']);

    Route::resource('data-policy', DataPolicyController::class)
        ->only(['index']);

    Route::resource('operators.profile', OperatorProfileEditController::class)
        ->only(['index', 'create', 'store']);

    # <<Minimum Configuration
    Route::get('/minimum-configuration-check/{operator}', [MinimumConfigurationController::class, 'hasPendingConfig'])
        ->name('configuration.check');

    Route::get('/next-configuration/{operator}', [MinimumConfigurationController::class, 'next'])
        ->name('configuration.next');
    # Minimum Configuration>>

    // << sub_operators
    Route::resource('sub_operators', SubOperatorController::class)
        ->except(['destroy']);

    Route::resource('sub_operators.destroy', SubOperatorDeleteController::class)
        ->only(['create', 'store'])
        ->parameters([
            'sub_operators' => 'operator'
        ])
        ->middleware('password.confirm');

    Route::resource('sub_operators.credit-limit', SubOperatorCreditLimitEditController::class)
        ->only(['create', 'store'])
        ->parameters([
            'sub_operators' => 'operator'
        ]);

    Route::resource('sub_operators.account-balance', SubOperatorAccountBalanceAddController::class)
        ->only(['create', 'store'])
        ->parameters([
            'sub_operators' => 'operator'
        ]);

    Route::resource('sub_operators.billing_profiles', SubOperatorBillingProfileController::class)
        ->only(['index', 'create', 'store'])
        ->parameters([
            'sub_operators' => 'operator'
        ]);
    // sub_operators >>

    // <<packages
    Route::resource('packages', PackageController::class)
        ->only(['index', 'edit', 'update', 'destroy']);

    Route::resource('master_packages', MasterPackageController::class)
        ->only(['show']);

    Route::resource('packages.replace', PackageReplaceController::class)
        ->only(['create', 'store']);

    Route::resource('operators.packages', OperatorPackageController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('operators.extend_package_validity', ExtendPackageValidityController::class)
        ->only(['create', 'store'])
        ->middleware('EAB', 'ECL');
    // packages>>

    // << recharge cards
    Route::resource('card_distributors', CardDistributorController::class)
        ->except(['show']);

    Route::resource('distributor_payments', CardDistributorPaymentsController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('distributor_payments_download', CardDistributorsPaymentsDownloadController::class)
        ->only(['create', 'store']);

    Route::resource('recharge_cards', RechargeCardController::class)
        ->only(['index', 'create', 'store', 'destroy'])
        ->middleware('EAB');

    Route::resource('recharge_cards_download', RechargeCardDownloadController::class)
        ->only(['create', 'store']);

    Route::resource('yearly_card_distributor_payments', YearlyCardDistributorPaymentController::class)
        ->only(['index']);
    // recharge cards>>

    Route::resource('managers', ManagerController::class)
        ->except(['show']);

    // << sms
    Route::resource('sms_histories', SmsHistoryController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::resource('sms-broadcast-jobs', SmsBroadcastJobController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('event_sms', EventSmsController::class)
        ->only(['index', 'show', 'edit', 'update'])
        ->parameters([
            'event_sms' => 'event_sms'
        ]);
    // sms >>

    // <<expenses
    Route::resource('expense_categories', ExpenseCategoryController::class)
        ->except(['show']);

    Route::resource('expense_categories.expense_subcategories', ExpenseSubcategoryController::class)
        ->shallow();

    Route::resource('expenses', ExpenseController::class)
        ->except(['show']);

    Route::get('expense/report', [ExpenseController::class, 'expenseReport'])
        ->name('expense.report');

    Route::get('expense/report/details', [ExpenseController::class, 'expenseDetails'])
        ->name('expense.report.details');

    Route::get('expense/report/download', [ExpenseController::class, 'downloadExpenseReport'])
        ->name('expense.report.download');
    // expenses>>

    // << incomes
    Route::resource('operators_incomes', OperatorsIncomeController::class)
        ->only(['index']);
    Route::resource('operators_incomes_summary', OperatorsIncomeSummaryController::class)
        ->only(['index']);
    // incomes >>

    Route::resource('incomes-vs-expenses', IncomeVsExpenseController::class)
        ->only(['index']);

    #start <<customer list, create & edit>>
    Route::get('/check-customers-uniqueness', [CustomerDuplicateValueCheckController::class, '__invoke']);

    Route::resource('customer_zones', CustomerZoneController::class)
        ->except(['show']);

    Route::resource('devices', DeviceController::class)
        ->except(['show']);

    Route::resource('custom_fields', CustomFieldController::class)
        ->except(['show']);

    Route::middleware(['payment.sms', 'pending.transaction', 'ECL', 'EAB'])->group(function () {

        Route::resource('temp_customers', TempCustomerController::class)
            ->only(['create', 'store', 'edit', 'update']);

        Route::resource('temp_customer.billing_profile', TempCustomerBillingProfileController::class)
            ->only(['create', 'store']);

        Route::resource('temp_customer.tech_info', TempCustomerTechInfoController::class)
            ->only(['create', 'store']);

        Route::resource('temp_customer.bill_info', TempCustomerBillInfoController::class)
            ->only(['create']);

        Route::resource('temp_customers.customers', CustomerCreateController::class)
            ->only(['create', 'store']);
    });

    Route::resource('customers', CustomerController::class)
        ->except(['create', 'store']);

    Route::resource('/customer-id', CustomerIdSearchController::class)
        ->only(['show']);

    Route::resource('/customer-mobiles', CustomerMobileSearchController::class)
        ->only(['index', 'show']);

    Route::resource('/customer-usernames', CustomerUsernameSearchController::class)
        ->only(['index', 'show'])
        ->where([
            'customer_username' => '.*',
        ]);

    Route::resource('/customer-names', CustomerNameSearchController::class)
        ->only(['index', 'show'])
        ->where([
            'customer_name' => '.*',
        ]);

    Route::resource('/global-customer-search', GlobalCustomerSearchController::class)
        ->only(['index', 'show'])
        ->where([
            'global_customer_search' => '.*',
        ]);

    Route::resource('/customer-details', CustomerDetailsController::class)
        ->only(['show'])
        ->parameters([
            'customer-details' => 'customer'
        ]);

    Route::resource('customers.custom_attributes', CustomerCustomAttributeController::class)
        ->only(['create', 'store']);

    Route::get('/customer-activate/{customer}', [CustomerActivateController::class, 'update'])
        ->name('customer-activate');

    Route::get('/customer-activate-options/{id}', [CustomerActivateOptionController::class, 'show'])
        ->name('customer-activate-options');

    Route::get('/customer-suspend/{customer}', [CustomerSuspendController::class, 'update'])
        ->name('customer-suspend');

    Route::get('/customer-disable/{customer}', [CustomerDisableController::class, 'update'])
        ->name('customer-disable');

    Route::get('/activate-fup/{customer}', [ActivateFupController::class, 'update'])
        ->name('activate-fup');

    Route::resource('/customer-package-change', CustomerPackageChangeController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'customer-package-change' => 'customer'
        ]);

    Route::get('/package-change-runtime-invoice/{customer}/{package}', [CustomerPackageChangeController::class, 'runtimeInvoice'])
        ->name('package-change.runtime-invoice');

    Route::resource('/ppp-daily-recharge', PppDailyRechargeController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'ppp-daily-recharge' => 'customer'
        ]);

    Route::get('/ppp-daily-recharge-runtime-invoice/{customer}/{package}', [PppDailyRechargeController::class, 'runtimeInvoice'])
        ->name('ppp-daily-recharge.runtime-invoice');

    Route::resource('/daily-billing-package-change', DailyBillingPackageChangeController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'daily-billing-package-change' => 'customer'
        ]);

    Route::get('/daily-billing-package-change/{customer}/{package}', [DailyBillingPackageChangeController::class, 'runtimeInvoice'])
        ->name('daily-billing-package-change.runtime-invoice');

    Route::resource('/hotspot-recharge', HotspotRechargeController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'hotspot-recharge' => 'customer'
        ]);

    Route::get('/hotspot-recharge-runtime-invoice/{customer}/{package}', [HotspotRechargeController::class, 'runtimeInvoice'])
        ->name('hotspot-recharge.runtime-invoice');

    Route::resource('/hotspot-package-change', HotspotPackageChangeController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'hotspot-package-change' => 'customer'
        ]);

    Route::get('/hotspot-package-change/{customer}/{package}', [HotspotPackageChangeController::class, 'runtimeInvoice'])
        ->name('hotspot-package-change.runtime-invoice');

    Route::resource('/customer-package-time-limit', CustomerTimeLimitController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'customer-package-time-limit' => 'customer'
        ]);

    Route::resource('/customer-package-speed-limit', CustomerSpeedLimitController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'customer-package-speed-limit' => 'customer'
        ]);

    Route::resource('/customer-package-volume-limit', CustomerVolumeLimitController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'customer-package-volume-limit' => 'customer'
        ]);

    Route::resource('/customer-billing-profile-edit', CustomerBillingProfileEditController::class)
        ->only(['edit', 'update'])
        ->parameters([
            'customer-billing-profile-edit' => 'customer'
        ]);

    Route::get('/billing-profile-edit-runtime-invoice/{customer}/{billing_profile}', [CustomerBillingProfileEditController::class, 'runtimeInvoice'])
        ->name('billing-profile-edit.runtime-invoice');

    Route::get('/mac-bind-create/{radacct}', [CustomerMacBindController::class, 'create'])
        ->name('mac-bind-create');

    Route::get('/mac-bind-destroy/{customer}', [CustomerMacBindController::class, 'destroy'])
        ->name('mac-bind-destroy');

    Route::resource('bulk-mac-bind', BulkMacBindController::class)
        ->only(['store']);

    Route::resource('customers.sms_histories', CustomersSmsHistoryCreateController::class)
        ->only(['create', 'store']);

    Route::resource('pppoe_customers_import', PPPoECustomersImportController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::resource('online_customers', OnlineCustomersController::class)
        ->only(['index', 'show']);

    Route::resource('/interface-traffic', PppoeInterfaceTrafficMonitorController::class)
        ->only(['show'])
        ->parameters([
            'interface-traffic' => 'radacct'
        ]);

    Route::resource('offline_customers', OfflineCustomerController::class)
        ->only(['index']);

    Route::post('/multiple-customer-update', [MultipleCustomerEditController::class, 'updateOrDestroy'])
        ->name('multiple-customer-update');

    Route::resource('download-users-downloadable', DownloadusersDownloadableController::class)
        ->only(['create', 'store']);

    Route::resource('download-users-uploadable', DownloadusersUploadableController::class)
        ->only(['create', 'store']);

    Route::resource('bulk-update-users', BulkUpdateUsersController::class)
        ->only(['create', 'store']);

    Route::resource('customers.advance_payment', CustomerAdvancePaymentController::class)
        ->only(['create', 'store']);

    Route::resource('customers.internet-history', InternetHistoryDownloadController::class)
        ->only(['create']);

    Route::resource('customers.custom_prices', CustomPriceController::class)
        ->except(['show']);

    Route::resource('customers.change_operator', OperatorChangeController::class)
        ->only(['create', 'store']);

    Route::resource('customers.suspend_date', SuspendDateEditController::class)
        ->only(['create', 'store']);

    Route::resource('customers.disconnect', PPPCustomerDisconnectController::class)
        ->only(['create']);

    Route::resource('customers.make_child', ChildCustomerAccountController::class)
        ->only(['create', 'store']);

    Route::get('/make_parent/{child}', [ChildCustomerAccountController::class, 'makeParent'])->name('customers.make_parent');

    Route::resource('customers.edit-ip', CustomerIpEditController::class)
        ->only(['create', 'store']);
    #end <<customer list, create & edit>>

    // << deleted customers
    Route::resource('deleted_customers', DeletedCustomerController::class)
        ->only(['index', 'destroy']);
    // deleted customers >>

    // << activity_logs
    Route::get('activity_logs', [PgsqlActivityLogController::class, 'index'])
        ->name('activity_logs.index');
    // activity_logs >>

    // <<Support Programme
    Route::get('support-programme-policy', [SupportProgrammePolicyController::class, 'index'])
        ->name('support_programme_policy.index');

    Route::get('affiliate-link', [AffiliateLinkController::class, 'index'])
        ->name('affiliate_link.index');

    Route::resource('affiliate-leads', AffiliateLeadsController::class)
        ->only(['index', 'create', 'store']);

    Route::get('support-programme-sales', [SupportProgrammeSalesController::class, 'index'])
        ->name('support_programme_sales.index');
    // Support Programme>>

    // <<VAT
    Route::resource('vat_profiles', VatProfileController::class)->except('show');
    Route::resource('vat_collections', VatCollectionController::class);
    // VAT>>

    Route::resource('ping-test', PingTestController::class)
        ->only(['create', 'store']);
});


/*
|--------------------------------------------------------------------------
| Developer Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['ACL', 'auth', '2FA'])->group(function () {

    Route::resource('payment_gateways', PaymentGatewayController::class)
        ->except(['show']);

    Route::resource('sms_gateways', SmsGatewayController::class)
        ->except(['show']);

    Route::get('screenshot', ScreenShotController::class);

    Route::resource('/operators-delete', OperatorDeleteController::class)
        ->only(['index', 'show'])
        ->parameters([
            'operators-delete' => 'operator'
        ]);

    Route::resource('foreign-routers', ForeignRouterController::class)
        ->only(['index', 'create']);

    Route::resource('subscription_discounts', SubscriptionDiscountController::class);

    Route::resource('max_subscription_payments', MaxSubscriptionPaymentController::class);

    Route::resource('minimum_sms_bills', MinimumSmsBillController::class);

    Route::resource('developer-notice-broadcast', DeveloperNoticeBroadcastController::class)
        ->only(['create', 'store']);

    Route::get('/mailable', function () {
        $operator = \App\Models\operator::find(43);

        return new \App\Mail\GreetingFromIspBillingSolution($operator);
    });

    Route::get('/mailable/accounts', function () {
        $payment = \App\Models\subscription_payment::find(7);

        return new \App\Mail\SoftwareSubscriptionPaymentReceived($payment);
    });

    Route::get('/mailable/sms_balance', function () {
        $balance = 100;
        return new \App\Mail\LowSmsBalance($balance);
    });

    Route::resource('vpn-pools', VpnPoolController::class)
        ->except(['show', 'edit', 'update']);

    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('LaravelLogs');

    Route::get('failed-logins', FailedLoginViewController::class)->name('failed-logins');
});


/*
|--------------------------------------------------------------------------
| Sales Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('self-registered-admins', SelfRegisteredAdminsController::class)
        ->only(['index']);

    Route::resource('operators.sales_comments', SalesCommentController::class)
        ->only(['create', 'store']);

    Route::get('/next-sales-comments/{operator}', [SalesCommentController::class, 'nextOperator'])
        ->name('next-sales-comments');
});



/*
|--------------------------------------------------------------------------
| Complaint Management
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    Route::resource('departments', DepartmentController::class)
        ->except(['show']);

    Route::resource('complain_categories', ComplainCategoryController::class)
        ->except(['show']);

    Route::resource('customers.customer_complains', CustomerComplainController::class)
        ->only(['create', 'store']);

    Route::resource('general-customer-complaints', GeneralComplaintController::class)
        ->only(['create', 'store']);

    Route::resource('customer_complains', CustomerComplainController::class)
        ->only(['index', 'show', 'destroy']);

    Route::resource('customer_complains.complain_comments', ComplainCommentController::class)
        ->only(['store']);

    Route::resource('customer_complains.acknowledge', ComplainAcknowledgeController::class)
        ->only(['create']);

    Route::resource('customer_complains.departments', ComplainDepartmentController::class)
        ->only(['store']);

    Route::resource('customer_complains.complain_categories', ComplainCategoryEditController::class)
        ->only(['store']);

    Route::resource('archived_customer_complains', ArchivedCustomerComplainController::class)
        ->only(['index']);

    Route::resource('complaint-reporting', ComplaintReportController::class)
        ->only(['index']);
});


/*
|--------------------------------------------------------------------------
| Questions and Answers
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

    Route::resource('questions', QuestionController::class);

    Route::resource('questions.question_options', QuestionOptionController::class)
        ->only(['create', 'store']);

    Route::resource('questions.question_answers', QuestionAnswerController::class)
        ->only(['create', 'store']);

    Route::resource('questions.question_explanations', QuestionExplanationController::class)
        ->only(['create', 'store']);

    Route::resource('exam', ExamController::class)
        ->only(['index', 'store', 'show'])
        ->parameters([
            'exam' => 'question'
        ]);
});

Route::middleware(['auth', 'role:group_admin'])->group(function () {
    Route::get('pppoe-import-from-xl', [PPPoEImportFromXLController::class, 'create'])->name('pppoe_import_from_xl.create');
    Route::post('pppoe-import-from-xl', [PPPoEImportFromXLController::class, 'store'])->name('pppoe_import_from_xl.store');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/ajax.php';
require __DIR__ . '/routes_of_customers.php';
require __DIR__ . '/accounting.php';
require __DIR__ . '/adminbillsnpayments.php';
require __DIR__ . '/card_distributors_ui_routes.php';

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/payment-statement', [OperatorsOnlinePaymentController::class, 'index'])->name('payment.statement');
    Route::get('/payment-statement/report', [OperatorsOnlinePaymentController::class, 'generateReport'])->name('payment.statement.report');
    Route::get('/group-admin/payment-statement', [OperatorPaymentStatementController::class, 'index'])->name('group_admin.payment.statement');
    Route::get('/group-admin/payment-statement/report', [OperatorPaymentStatementController::class, 'generateReport'])->name('group_admin.payment.statement.report');
    Route::get('/group-admin/operator-suboperator-payments', [OperatorSubOperatorPaymentsController::class, 'index'])->name('group_admin.operator_suboperator_payments.index');
    Route::get('/group-admin/operator-suboperator-payments/report', [OperatorSubOperatorPaymentsController::class, 'generateReport'])->name('group_admin.operator_suboperator_payments.report');
});

/*
|--------------------------------------------------------------------------
| Activity Logs Routes (New Feature)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/activity-logs-new', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity_logs_new.index');
    
    // Device Monitor Routes
    Route::get('/device-monitor/status', [App\Http\Controllers\DeviceMonitorController::class, 'getDeviceStatus'])->name('device_monitor.status');
    
    // Device Monitor CRUD (only for super_admin, developer, group_admin)
    Route::middleware(['role:super_admin,developer,group_admin'])->group(function () {
        Route::resource('device-monitors', App\Http\Controllers\DeviceMonitorController::class);
    });
});
