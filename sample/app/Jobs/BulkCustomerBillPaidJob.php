<?php

namespace App\Jobs;

use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Models\bulk_customer_bill_paid;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkCustomerBillPaidJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The operator instance.
     *
     * @var \App\Models\operator
     */
    protected $operator;

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
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->operator->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $operator = $this->operator;

        $bills = bulk_customer_bill_paid::where('requester_id', $operator->id)->get();

        foreach ($bills as $bill) {

            $customer_bill = customer_bill::find($bill->customer_bill_id);

            if (!$customer_bill) {
                continue;
            }

            if ($operator->cannot('receivePayment', $customer_bill)) {
                return 0;
            }

            $customer_payment = new customer_payment();
            $customer_payment->mgid = $customer_bill->mgid;
            $customer_payment->gid = $customer_bill->gid;
            $customer_payment->operator_id = $customer_bill->operator_id;
            $customer_payment->cash_collector_id = $operator->id;
            $customer_payment->parent_customer_id = $customer_bill->parent_customer_id;
            $customer_payment->customer_id = $customer_bill->customer_id;
            $customer_payment->customer_bill_id = $customer_bill->id;
            $customer_payment->package_id = $customer_bill->package_id;
            $customer_payment->validity_period = $customer_bill->validity_period;
            $customer_payment->payment_gateway_name = 'Cash';
            $customer_payment->mobile = $customer_bill->mobile;
            $customer_payment->name = $customer_bill->name;
            $customer_payment->username = $customer_bill->username;
            $customer_payment->type = 'Cash';
            $customer_payment->payment_mode = 'prepaid';
            $customer_payment->pay_status = 'Successful';
            $customer_payment->amount_paid = $customer_bill->amount;
            $customer_payment->store_amount = $customer_bill->amount;
            $customer_payment->discount = 0;
            $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
            $customer_payment->date = date(config('app.date_format'));
            $customer_payment->week = date(config('app.week_format'));
            $customer_payment->month = date(config('app.month_format'));
            $customer_payment->year = date(config('app.year_format'));
            $customer_payment->used = 0;
            $customer_payment->require_sms_notice = 0;
            $customer_payment->require_accounting = 1;
            $customer_payment->purpose = $customer_bill->purpose;
            $customer_payment->save();

            // process payment
            CustomersPaymentProcessController::doStore($customer_payment);

            // delete request
            $bill->delete();
        }
    }
}
