<?php

namespace App\Console\Commands;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\TelegramEmergencyNotificationController;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSmsBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:generateBill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SMS Bill';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('sms_bill')->info('sms:generateBill started');

        $controller = new SmsGatewayController();
        $controller->generateBill();

        // Report
        $sms_gateways = sms_gateway::where('saleable', 1)->get();
        foreach ($sms_gateways as $sms_gateway) {
            $unbilled_sms_count = sms_history::where('sms_gateway_id', $sms_gateway->id)
                ->where('sms_bill_id', 0)
                ->where('cost_checked', 0)
                ->count();

            if ($unbilled_sms_count > 100) {
                $message = 'unbilled sms count : ' . $unbilled_sms_count;
                TelegramEmergencyNotificationController::send($message);
            }
        }

        // non saleable
        $sms_gateways = sms_gateway::where('saleable', 0)->get();
        foreach ($sms_gateways as $sms_gateway) {
            sms_history::where('sms_gateway_id', $sms_gateway->id)->update(['sms_bill_id' => 1, 'cost_checked' => 1]);
        }

        Log::channel('sms_bill')->info('sms:generateBill finished');

        return 0;
    }
}
