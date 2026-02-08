<?php

namespace App\Providers;

use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\account;
use App\Models\activity_log;
use App\Models\billing_profile;
use App\Models\cash_out;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\device_monitor;
use App\Models\event_sms;
use App\Models\expense;
use App\Models\expiration_notifier;
use App\Models\Freeradius\customer;
use App\Models\ipv4pool;
use App\Models\ipv6pool;
use App\Models\master_package;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\operator;
use App\Models\package;
use App\Models\pppoe_profile;
use App\Models\recharge_card;
use App\Models\temp_billing_profile;
use App\Models\vat_profile;
use App\Models\vpn_account;
use App\Models\vpn_pool;
use App\Policies\AccountPolicy;
use App\Policies\ActivityLogPolicy;
use App\Policies\BillingProfilePolicy;
use App\Policies\CanCustomerPayPolicy;
use App\Policies\CanTheCardBeUsedPolicy;
use App\Policies\CashOutPolicy;
use App\Policies\CustomerBillPolicy;
use App\Policies\CustomerPaymentPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DeviceMonitorPolicy;
use App\Policies\EventSmsPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\ExpirationNotifierPolicy;
use App\Policies\Ipv4poolPolicy;
use App\Policies\Ipv6poolPolicy;
use App\Policies\MasterPackagePolicy;
use App\Policies\OperatorPolicy;
use App\Policies\PackagePolicy;
use App\Policies\PPPoeProfilePolicy;
use App\Policies\TempBillingProfilePolicy;
use App\Policies\VatProfilePolicy;
use App\Policies\VpnAccountPolicy;
use App\Policies\VpnPoolPolicy;
use Illuminate\Auth\Access\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        customer::class => CustomerPolicy::class,
        package::class => PackagePolicy::class,
        ipv4pool::class => Ipv4poolPolicy::class,
        ipv6pool::class => Ipv6poolPolicy::class,
        pppoe_profile::class => PPPoeProfilePolicy::class,
        operator::class => OperatorPolicy::class,
        billing_profile::class => BillingProfilePolicy::class,
        customer_bill::class => CustomerBillPolicy::class,
        customer_payment::class => CustomerPaymentPolicy::class,
        account::class => AccountPolicy::class,
        temp_billing_profile::class => TempBillingProfilePolicy::class,
        master_package::class => MasterPackagePolicy::class,
        expense::class => ExpensePolicy::class,
        vpn_pool::class => VpnPoolPolicy::class,
        vpn_account::class => VpnAccountPolicy::class,
        event_sms::class => EventSmsPolicy::class,
        cash_out::class => CashOutPolicy::class,
        vat_profile::class => VatProfilePolicy::class,
        expiration_notifier::class => ExpirationNotifierPolicy::class,
        activity_log::class => ActivityLogPolicy::class,
        device_monitor::class => DeviceMonitorPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('PayFor', function (customer $customer, PaymentPurpose $paymentPurpose) {
            return new CanCustomerPayPolicy($customer, $paymentPurpose);
        });

        Gate::define('useRechargeCard', function (customer $customer, recharge_card $recharge_card) {
            $p = new CanTheCardBeUsedPolicy($customer, $recharge_card);
            return $p->canUseRechargeCard();
        });

        Gate::define('accessSuperAdminPanel', function (operator $user) {
            return $user->role === 'super_admin' || $user->role === 'developer';
        });

        Gate::define('accessGroupAdminPanel', function (operator $user) {
            return $user->role === 'group_admin' || $user->role === 'developer';
        });

        Gate::define('editProfile', function (operator $user) {

            if ($user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            return false;
        });

        // << Dashboard
        Gate::define('viewWidgets', function (operator $user) {

            if ($user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            if ($user->role == 'manager') {
                return $user->permissions->contains('Dashboard');
            }

            return false;
        });

        Gate::define('viewCustomerDetails', function (operator $user) {
            if ($user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            if ($user->role == 'manager') {
                return $user->permissions->contains('view-customer-details');
            }

            return false;
        });

        Gate::define('viewCustomerBills', function (operator $user) {
            if ($user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            if ($user->role == 'manager') {
                return $user->permissions->contains('print-invoice');
            }

            return false;
        });

        Gate::define('viewCustomerPayments', function (operator $user) {
            if ($user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            if ($user->role == 'manager') {
                return $user->permissions->contains('view-customer-payments');
            }

            return false;
        });

        Gate::define('viewOnlineCustomers', function (operator $user) {
            if ($user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            if ($user->role == 'manager') {
                return $user->permissions->contains('view-online-customers');
            }

            return false;
        });

        Gate::define('viewSmsHistories', function (operator $user) {
            if ($user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            return false;
        });

        // Dashboard >>

        // usePackage
        Gate::define('usePackage', function (customer $customer, package $package) {

            if ($customer->operator_id != $package->operator_id) {
                return Response::deny('The operator should be the same for the customer and the package.');
            }

            if ($customer->connection_type != $package->master_package->connection_type) {
                return Response::deny('The connection type must be same for the customer and the package. We found the customer connection type: ' . $customer->connection_type . ' but the package connection type: ' . $package->master_package->connection_type);
            }

            return true;
        });

        // Download Payments
        Gate::define('downloadPayments', function (operator $user) {
            if ($user->role == 'super_admin' || $user->role == 'group_admin' || $user->role == 'operator' || $user->role == 'sub_operator') {
                return true;
            }

            return false;
        });

        // Support Programme
        Gate::define('enrolInSupportProgramme', function (operator $user) {

            if (config('consumer.has_support_programme') == false) {
                return false;
            }

            if (config('consumer.support_programme_director') == 0) {
                return false;
            }

            if ($user->role == 'group_admin') {
                return true;
            }

            return false;
        });

        // recharge customer account
        Gate::define('recharge', function (operator $operator, $amount) {

            // group_admin
            if ($operator->role === 'group_admin') {
                return true;
            }

            // manager
            if ($operator->role === 'manager') {
                if ($operator->permissions->contains('receive-payment')) {
                    if ($operator->group_admin->account_type == 'debit') {
                        if ($operator->group_admin->account_balance >= $amount) {
                            return true;
                        }
                    } else {
                        if ($operator->group_admin->credit_limit == 0) {
                            return true;
                        }
                        if ($operator->group_admin->creditBalance >= $amount) {
                            return true;
                        }
                    }
                }
            }

            // operator & sub_operator
            if ($operator->account_type == 'debit') {
                if ($operator->account_balance >= $amount) {
                    return true;
                }
            } else {
                if ($operator->credit_limit == 0) {
                    return true;
                }
                if ($operator->creditBalance >= $amount) {
                    return true;
                }
            }

            // default
            return false;
        });
    }
}
