<?php

namespace App\Jobs;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\sms_broadcast_job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsBroadcastJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The sms_broadcast_job instance.
     *
     * @var \App\Models\sms_broadcast_job
     */
    public $sms_broadcast_job;

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
        return $this->sms_broadcast_job->id;
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(sms_broadcast_job $sms_broadcast_job)
    {
        $this->sms_broadcast_job = $sms_broadcast_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sms_broadcast_job = $this->sms_broadcast_job;

        $filter = json_decode($sms_broadcast_job->filter, true);

        $operator = operator::find($sms_broadcast_job->operator_id);

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customers = $model->where($filter)->get();

        $controller = new SmsGatewayController();

        foreach ($customers as $customer) {
            $mobile = validate_mobile($customer->mobile);
            if ($mobile) {
                $controller->sendSms($operator, $mobile, $sms_broadcast_job->message, $customer->id);
            }
        }

        $sms_broadcast_job->delete();
    }
}
