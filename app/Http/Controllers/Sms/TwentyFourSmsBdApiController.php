<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Support\Facades\Http;

class TwentyFourSmsBdApiController extends Controller
{
    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {
        $sms_gateway = sms_gateway::find($sms_history->sms_gateway_id);

        $response = Http::asForm()->post('https://24bulksms.com/24bulksms/api/api-sms-send', [
            'sender_id' => $sms_gateway->username,
            'api_key' => $sms_gateway->password,
            'user_email' => $sms_gateway->from_number,
            'mobile_no' => $sms_history->to_number,
            'message' => $sms_history->sms_body,
        ]);

        $response = json_decode($response, true);

        if (!$response) {
            return 0;
        }

        if (array_key_exists('message', $response)) {
            if ($response['message'] == 'Successfull') {
                $sms_history->status_text = 'Successful';
                $sms_history->save();
            } else {
                $sms_history->status_text = 'Failed';
                $sms_history->status_details = $response['message'];
                $sms_history->save();
            }
        }
    }
}
