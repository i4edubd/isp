<?php

namespace App\Jobs;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\billing_profile;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastPaymentLinks implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The operator_id.
     *
     * @var int
     */
    protected $operator_id;

    /**
     * The text_message.
     *
     * @var string
     */
    protected $text_message;

    /**
     * The text_message.
     *
     * @var string
     */
    protected $billing_profile_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($operator_id, $text_message, $billing_profile_id)
    {
        $this->operator_id = $operator_id;
        $this->text_message = $text_message;
        $this->billing_profile_id = $billing_profile_id;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->operator_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $billing_profile_id = $this->billing_profile_id;

        $billing_profile = billing_profile::find($billing_profile_id);

        $operator_id = $this->operator_id;

        $operator = operator::find($operator_id);

        if ($billing_profile) {
            $where = [
                ['operator_id', '=', $operator->id],
                ['payment_status', '=', 'billed'],
                ['billing_profile_id', '=', $billing_profile->id],
            ];
        } else {
            $where = [
                ['operator_id', '=', $operator->id],
                ['payment_status', '=', 'billed'],
            ];
        }

        $model = new customer();
        $model->setConnection($operator->radius_db_connection);
        $customers = $model->where($where)->get();

        foreach ($customers as $customer) {

            $bill_where = [
                ['operator_id', '=', $operator->id],
                ['customer_id', '=', $customer->id],
            ];

            $bill = customer_bill::where($bill_where)->first();

            if ($bill && validate_mobile($customer->mobile)) {
                $text_message = $this->text_message . ' ' . route('root') . "?cid=" . $bill->customer_id . "&bid=" . $bill->id;
                $sms_gateway = new SmsGatewayController();
                $sms_gateway->sendSms($operator, $customer->mobile, $text_message, $customer->id);
            }
        }
    }
}
