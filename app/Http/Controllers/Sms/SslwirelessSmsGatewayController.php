<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SslwirelessSmsGatewayController extends Controller
{
    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {
        $sms_gateway = sms_gateway::find($sms_history->sms_gateway_id);

        $response = Http::get("https://smsplus.sslwireless.com/api/v3/send-sms", [
            "api_token" => $sms_gateway->password,
            "sid" => $sms_gateway->from_number,
            "msisdn" => $sms_history->to_number,
            "sms" => $sms_history->sms_body,
            "csms_id" => $sms_history->id,
        ]);

        $response = json_decode($response, true);

        if(is_array($response) == false){
            return 0;
        }

        if ($response['status'] == 'SUCCESS') {
            $sms_history->status_text = 'Successful';
        } else {
            $sms_history->status_text = 'Failed';
            $sms_history->status_details = $response['error_message'];
        }

        $sms_history->save();
    }
}
