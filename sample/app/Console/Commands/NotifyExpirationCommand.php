<?php

namespace App\Console\Commands;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\expiration_notifier;
use App\Models\Freeradius\customer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyExpirationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'notify expiration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('local.host_type', 'node') !== 'central') {
            return Command::SUCCESS;
        }

        $expiration_notifiers = expiration_notifier::all();

        foreach ($expiration_notifiers as $expiration_notifier) {
            // inactive
            if ($expiration_notifier->status == 'inactive') {
                continue;
            }

            // no sms gateway
            $operator = $expiration_notifier->operator;
            $sms_gw = SmsGatewayController::getSmsGw($operator);
            if ($sms_gw->id == 0) {
                continue;
            }

            // filter
            $checked_connection_types = json_decode($expiration_notifier->connection_types, true);
            $checked_billing_types = json_decode($expiration_notifier->billing_types, true);
            if (count($checked_connection_types) == 0 || count($checked_billing_types) == 0) {
                continue;
            }

            foreach ($checked_connection_types as $connection_type) {
                foreach ($checked_billing_types as $billing_type) {
                    $model = new customer();
                    $model->setConnection($operator->node_connection);
                    $customers = $model->where('operator_id', $expiration_notifier->operator_id)->where('connection_type', $connection_type)->where('billing_type', $billing_type)->get();
                    foreach ($customers as $customer) {
                        // invalid mobile
                        $mobile_number = validate_mobile($customer->mobile, getTimeZone($operator->id));
                        if (!$mobile_number) {
                            continue;
                        }
                        // already expired
                        if (Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($operator->id), 'en')->lessThan(Carbon::now(getTimeZone($operator->id)))) {
                            continue;
                        }
                        // send notice
                        if (Carbon::now(getTimeZone($operator->id))->diffInDays(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($operator->id), 'en')) == $expiration_notifier->notify_before) {
                            $message = SmsGenerator::expirationNotificationMsg($operator, $customer);
                            SmsGatewayController::sendSms($operator, $mobile_number, $message, $customer->id);
                        }
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
