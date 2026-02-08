<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Support\Facades\Http;

class SmsinbdController extends Controller
{
    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {
        $sms_gateway = sms_gateway::find($sms_history->sms_gateway_id);

        $response = Http::asForm()->post('https://api.smsinbd.com/sms-api/sendsms?', [
            'api_token' => $sms_gateway->password,
            'senderid' => $sms_gateway->username,
            'contact_number' => $sms_history->to_number,
            'message' => $sms_history->sms_body,
        ]);

        $response = json_decode($response, true);

        if (!$response) {
            return 0;
        }

        if (array_key_exists('status', $response)) {
            if ($response['status'] == 'success') {
                $sms_history->status_text = 'Successful';
                $sms_history->messageid = $response['smsid'];
                $sms_history->sms_count = $response['SmsCount'];
                $sms_history->save();
            } else {
                $sms_history->status_text = 'Failed';
                $sms_history->status_details = $response['message'];
                $sms_history->save();
            }
        }
    }
}
