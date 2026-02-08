<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdnSmsController extends Controller
{

    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {
        $sms_gateway = sms_gateway::find($sms_history->sms_gateway_id);

        $response = Http::post('https://portal.adnsms.com/api/v1/secure/send-sms', [
            'api_key' => $sms_gateway->username,
            'api_secret' => $sms_gateway->password,
            'request_type' => 'SINGLE_SMS',
            'message_type' => 'TEXT',
            'mobile' => $sms_history->to_number,
            'message_body' => $sms_history->sms_body,
        ]);

        $response = json_decode($response, true);

        if ($response['api_response_code'] == 200) {

            $status = Http::post('https://portal.adnsms.com/api/v1/secure/sms-status', [
                'api_key' => $sms_gateway->username,
                'api_secret' => $sms_gateway->password,
                'sms_uid' => $response['sms_uid'],
            ]);

            $sms_status = json_decode($status, true);

            $smscount = $sms_status['sms']['sms_quantity'];
            $charge_per_sms = $sms_gateway->unit_price;

            $sms_history->status_text = 'Successful';
            $sms_history->sms_count = $smscount;
            $sms_history->sms_cost = $smscount * $charge_per_sms;
            $sms_history->save();
        } else {
            $sms_history->status_text = 'Failed';
            $sms_history->status_details = 'SMS Gateway Error : ' . $response['error']['error_message'];
            $sms_history->save();
        }
    }
}
