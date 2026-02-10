<?php

namespace App\Jobs;

use App\Http\Controllers\BillingHelper;
use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\enum\BillingTerms;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\customer_payment;
use App\Models\extend_package_validity;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Gate;

class ExtendPackageValidityJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The operator instance.
     *
     * @var \App\Models\operator
     */
    public $operator;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->operator->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(operator $operator)
    {
        $this->operator = $operator;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $operator = $this->operator;

        $requests = extend_package_validity::where('operator_id', $operator->id)->get();

        foreach ($requests as $request) {

            $model = new customer();
            $model->setConnection($operator->node_connection);
            $customer = $model->find($request->customer_id);

            if (!$customer) {
                continue;
            }
            $package = package::find($request->package_id);

            $package_started_at = BillingHelper::getStartingDate($customer, $package);
            $package_expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $package_started_at, getTimeZone($operator->id), 'en')->addDays($request->validity)->isoFormat(config('app.expiry_time_format'));

            $validity_minutes = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $package_started_at, getTimeZone($operator->id), 'en')->diffInMinutes(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $package_expired_at, getTimeZone($operator->id), 'en'));
            $invoice = BillingHelper::getRuntimeInvoice($customer, $package, BillingTerms::INTERVAL_UNIT_MINUTE->value, $validity_minutes);

            // authorization
            $operator_price = $invoice->get('operators_payable_amount');
            if (Gate::forUser($operator)->denies('recharge', [$operator_price])) {
                return 0;
            }

            $currency = getCurrency($customer->operator_id);

            $customer_payment = new customer_payment();
            $customer_payment->mgid = $customer->mgid;
            $customer_payment->gid = $customer->gid;
            $customer_payment->operator_id = $customer->operator_id;
            $customer_payment->cash_collector_id = $operator->id;
            $customer_payment->parent_customer_id = $customer->parent_id;
            $customer_payment->customer_id = $customer->id;
            $customer_payment->customer_bill_id = 0;
            $customer_payment->package_id = $package->id;
            $customer_payment->payment_gateway_name = 'Cash';
            $customer_payment->mobile = $customer->mobile;
            $customer_payment->name = $customer->name;
            $customer_payment->username = $customer->username;
            $customer_payment->type = 'Cash';
            $customer_payment->pay_status = 'Successful';
            $customer_payment->currency = $currency;
            $customer_payment->amount_paid = $invoice->get('customers_payable_amount');
            $customer_payment->store_amount = $invoice->get('customers_payable_amount');
            $customer_payment->discount = 0;
            $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
            $customer_payment->date = date(config('app.date_format'));
            $customer_payment->week = date(config('app.week_format'));
            $customer_payment->month = date(config('app.month_format'));
            $customer_payment->year = date(config('app.year_format'));
            $customer_payment->used = 0;
            $customer_payment->require_sms_notice = 0;
            $customer_payment->package_started_at = $invoice->get('package_started_at');
            $customer_payment->package_expired_at = $invoice->get('package_expired_at');
            $customer_payment->purpose = PaymentPurpose::PACKAGE_PURCHASE->value;
            $customer_payment->save();
            // process payment
            CustomersPaymentProcessController::doStore($customer_payment);

            // cleaning
            $request->delete();
        }
    }
}
