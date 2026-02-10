<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BulksmsbdApiController extends Controller
{

    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {

        $sms_gateway = sms_gateway::find($sms_history->sms_gateway_id);

        $response = Http::asForm()->post('http://66.45.237.70/api.php?', [
            'username' => $sms_gateway->username,
            'password' => $sms_gateway->password,
            'number' => $sms_history->to_number,
            'message' => $sms_history->sms_body,
        ]);

        $response_array = explode('|', $response);

        $status = $response_array[0];

        if ($status == 1101) {
            $unit = $response_array[2];
            $sms_history->status_text = 'Successful';
            $sms_history->sms_count = $unit;
            $sms_history->sms_cost = $unit * $sms_gateway->unit_price;
            $sms_history->save();
        } else {
            $sms_history->status_text = 'Failed';
            $sms_history->status_details = 'SMS Gateway Error : ' . $status;
            $sms_history->save();
        }
    }
}
