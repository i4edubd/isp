<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BdSmartPaySmsController extends Controller
{
    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {
        $sms_gateway = sms_gateway::find($sms_history->sms_gateway_id);

        $response =  Http::asForm()->post('http://bdsmartpay.com/sms/smsapi.php', [
            'mobile' => $sms_history->to_number,
            'message' => $sms_history->sms_body,
            'username' => $sms_gateway->username,
            'password' => $sms_gateway->password,
            'sms_title' => $sms_gateway->from_number,
        ]);

        if ($response == '000') {
            $sms_history->status_text = 'Successful';
            $sms_history->sms_count = 1;
            $sms_history->sms_cost = $sms_gateway->unit_price;
            $sms_history->save();
        } else {
            switch ($response) {
                case '001':
                    $error = 'Message send Failed';
                    break;

                case '002':
                    $error = 'You have no balance in account';
                    break;

                default:
                    $error = 'Authentication Failed';
                    break;
            }
            $sms_history->status_text = 'Failed';
            $sms_history->status_details = $error;
            $sms_history->save();
        }

        return 0;
    }
}
