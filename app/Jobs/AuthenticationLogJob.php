<?php

namespace App\Jobs;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\authentication_log;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AuthenticationLogJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * The Event
     *
     * @var string
     */
    protected $event;

    /**
     * The operator instance
     *
     * @var \App\Models\operator
     */
    protected $operator;

    /**
     * The Event
     *
     * @var string
     */
    protected $ip_address;

    /**
     * The Event
     *
     * @var string
     */
    protected $user_agent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $event, operator $operator, string $ip_address, string $user_agent)
    {
        $this->event = $event;
        $this->operator = $operator;
        $this->ip_address = $ip_address;
        $this->user_agent = $user_agent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $event = $this->event;
        $operator = $this->operator;
        $ip_address = $this->ip_address;
        $user_agent = $this->user_agent;

        $authentication_log = new authentication_log();
        $authentication_log->operator_id = $operator->id;
        $authentication_log->ip_address = $ip_address;
        $authentication_log->user_agent = $user_agent;
        if ($event == 'login') {
            $authentication_log->login_at = Carbon::now();
        }
        if ($event == 'logout') {
            $authentication_log->logout_at = Carbon::now();
        }
        $authentication_log->save();

        // Device Identification
        if ($event == 'login') {
            if (authentication_log::where('operator_id', $operator->id)->where('ip_address', $authentication_log->ip_address)->where('user_agent', $authentication_log->user_agent)->count() == 1) {
                if ($operator->device_identification_enabled) {
                    $sms_operator =  match ($operator->role) {
                        'developer' => operator::where('role', 'super_admin')->first(),
                        'manager' => operator::where('id', $operator->gid)->first(),
                        default => $operator,
                    };
                    $otp = random_int(1000, 9999);
                    $operator->two_factor_recovery_codes = $otp;
                    $operator->save();
                    $message = SmsGenerator::OTP($operator, $otp);
                    $sms_gateway = new SmsGatewayController();
                    try {
                        $sms_gateway->sendSms($sms_operator, $operator->mobile, $message, $operator->id);
                        $operator->device_identification_pending = 1;
                        $operator->save();
                    } catch (\Throwable $th) {
                        Log::channel('stack')->error('@AuthenticationLogJob => ' . $th);
                        $operator->device_identification_pending = 0;
                        $operator->save();
                    }
                }
            }
        }
    }
}
