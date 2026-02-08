<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RobiSmsApiController extends Controller
{
    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {

        $sms_gateway = sms_gateway::find($sms_history->sms_gateway_id);

        $response = Http::asForm()->post('https://api.mobireach.com.bd/SendTextMessage?', [
            'Username' => $sms_gateway->username,
            'Password' => $sms_gateway->password,
            'From' => $sms_gateway->from_number,
            'To' => $sms_history->to_number,
            'Message' => $sms_history->sms_body,
        ]);

        // Convert xml string into an object
        $object = simplexml_load_string($response);

        // Convert into json
        $json = json_encode($object);

        // debug
        /*
        if (config('consumer.debug_sms')) {
            Storage::put('mobireach/response.txt', $json);
        }
        */

        // Convert into associative array
        $response_array = json_decode($json, true);

        if ($response_array['ServiceClass']['Status'] == 0 || $response_array['ServiceClass']['StatusText'] == 'success') {
            $unit = $response_array['ServiceClass']['SMSCount'];
            $sms_history->messageid = $response_array['ServiceClass']['MessageId'];
            $sms_history->status_text = 'Successful';
            $sms_history->sms_count = $unit;
            $sms_history->sms_cost = $unit * $sms_gateway->unit_price;
            $sms_history->save();
        } else {
            $sms_history->status_text = 'Failed';
            $sms_history->status_details = $response_array['ServiceClass']['ErrorText'];
            $sms_history->save();
        }
    }
}
