<?php

namespace App\Jobs;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyDues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The operator_id.
     *
     * @var int
     */
    protected $operator_id;

    /**
     * The due_date.
     *
     * @var int
     */
    protected $due_date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($operator_id, $due_date)
    {
        $this->operator_id = $operator_id;
        $this->due_date = $due_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $operator_id = $this->operator_id;

        $country_code = getCountryCode($operator_id);

        $due_date = $this->due_date;

        $operator = operator::find($operator_id);

        $date = date($due_date . '-m-Y');

        $where = [
            ['operator_id', '=', $operator->id],
            ['due_date', '=', $date],
        ];

        $bills = customer_bill::where($where)->get();

        $bills = $bills->groupBy('customer_id');

        foreach ($bills as $customer_id => $bill) {
            $model = new customer();
            $model->setConnection($operator->node_connection);
            $customer = $model->find($customer_id);

            if (!$customer) {
                continue;
            }

            $mobile = validate_mobile($customer->mobile, $country_code);
            if (!$mobile) {
                continue;
            }

            $text_message = SmsGenerator::dueNoticeMsg($operator, $customer);

            SmsGatewayController::sendSms($operator, $mobile, $text_message, $customer->id);
        }
    }
}
