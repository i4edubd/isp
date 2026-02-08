<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Models\sms_gateway;
use App\Models\sms_history;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElitbuzzFamilySMSController extends Controller
{
    /**
     * Get Sms Post Url
     *
     * @param  \App\Models\sms_gateway  $sms_gateway
     * @return string
     */
    public static function getSmsPostUrl(sms_gateway $sms_gateway)
    {
        return match ($sms_gateway->provider_name) {
            'bangladeshsms' => 'http://bangladeshsms.com/smsapi?',
            'm2mbd' => 'http://bulksms.m2mbd.net/smsapi?',
            'maestro' => 'https://sms.maestro.com.bd/api/smsapi?',
            'btssms' => 'http://btssms.com/smsapi?',
            '880sms' => 'https://880sms.com/smsapi?',
            'elitbuzz' => 'https://msg.elitbuzz-bd.com/smsapi?',
            'brandsms' => 'http://brandsms.itbd.info/smsapi?',
            'metrotel' => 'http://portal.metrotel.com.bd/smsapi?',
            'dianahost' => 'http://esms.dianahost.com/smsapi?',
            'dhakasoftbd' => 'http://panel.dhakasoftbd.com/smsapi?',
        };
    }

    /**
     * Get Delivery Report Url
     *
     * @param  \App\Models\sms_gateway  $sms_gateway
     * @param string $sms_shoot_id
     * @return string
     */
    public static function getDeliveryReportUrl(sms_gateway $sms_gateway, string $sms_shoot_id)
    {
        return match ($sms_gateway->provider_name) {
            'bangladeshsms' => 'http://bangladeshsms.com/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'm2mbd' => 'http://bulksms.m2mbd.net/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'maestro' => 'https://sms.maestro.com.bd/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'btssms' => 'http://btssms.com/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            '880sms' => 'https://880sms.com/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'elitbuzz' => 'https://msg.elitbuzz-bd.com/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'brandsms' => 'http://brandsms.itbd.info/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'metrotel' => 'http://portal.metrotel.com.bd/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'dianahost' => 'http://esms.dianahost.com/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
            'dhakasoftbd' => 'http://panel.dhakasoftbd.com/miscapi/' . $sms_gateway->password . '/' . 'getDLR/' . $sms_shoot_id,
        };
    }

    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     */
    public function sendSms(sms_history $sms_history)
    {
        $sms_gateway = CacheController::getSmsGateway($sms_history->sms_gateway_id);

        $response = Http::asForm()->post(self::getSmsPostUrl($sms_gateway), [
            'api_key' => $sms_gateway->password,
            'type' => 'text',
            'contacts' => $sms_history->to_number,
            'msg' => $sms_history->sms_body,
            'senderid' => $sms_gateway->from_number,
        ]);

        $response_array = explode(':', $response);

        if (trim($response_array[0]) == 'SMS SUBMITTED') {

            $dlrID = trim(explode('ID -', $response)[1]);

            try {
                $report = Http::retry(5, 100)->asForm()->get(self::getDeliveryReportUrl($sms_gateway, $dlrID));

                // Convert xml string into an object
                $object = simplexml_load_string($report);

                // Convert into json
                $json = json_encode($object);

                // Convert into associative array
                $report_array = json_decode($json, true);

                //sms count
                if (is_array($report_array)) {
                    if (array_key_exists('message', $report_array)) {
                        $smscount = $report_array['message']['Parts'];
                    } else {
                        $smscount = SmsGatewayController::getSMSCount($sms_history->sms_body);
                    }
                } else {
                    $smscount = SmsGatewayController::getSMSCount($sms_history->sms_body);
                }
            } catch (\Throwable $th) {
                Log::channel('sms_bill')->error($th->getMessage());
                $smscount = SmsGatewayController::getSMSCount($sms_history->sms_body);
            }

            // sms charge
            $charge_per_sms = $sms_gateway->unit_price;

            //save sms history
            $sms_history->messageid = $dlrID;
            $sms_history->status_text = 'Successful';
            $sms_history->sms_count = $smscount;
            $sms_history->sms_cost = $smscount * $charge_per_sms;
            $sms_history->cost_checked = 1;
            $sms_history->save();
        } else {
            $sms_history->status_text = 'Successful';
            $sms_history->status_details = 'Gateway : ' . $response;
            $sms_history->save();
        }
    }

    /**
     * Send SMS
     *
     * @param  \App\Models\sms_history  $sms_history
     * @param  \App\Models\sms_gateway  $sms_gateway
     */
    public static function checkCost(sms_history $sms_history, sms_gateway $sms_gateway)
    {
        if (is_null($sms_history->messageid)) {
            self::manualBill($sms_history, $sms_gateway);
            return 0;
        }

        try {

            $report_url = self::getDeliveryReportUrl($sms_gateway, $sms_history->messageid);

            $report = file_get_contents($report_url);

            // Convert xml string into an object
            $object = simplexml_load_string($report);

            // Convert into json
            $json = json_encode($object);

            // Convert into associative array
            $report_array = json_decode($json, true);

            //sms count & charge
            if (is_array($report_array)) {
                if (array_key_exists('message', $report_array)) {
                    $smscount = $report_array['message']['Parts'];
                    $charge_per_sms = $sms_gateway->unit_price;
                    $sms_history->sms_count = $smscount;
                    $sms_history->sms_cost = $smscount * $charge_per_sms;
                    $sms_history->cost_checked = 1;
                    $sms_history->save();
                    return 0;
                } else {
                    self::manualBill($sms_history, $sms_gateway);
                    return 0;
                }
            } else {
                self::manualBill($sms_history, $sms_gateway);
                return 0;
            }
        } catch (\Throwable $th) {
            self::manualBill($sms_history, $sms_gateway);
            Log::channel('sms_bill')->error($th->getMessage());
            return 0;
        }
    }

    /**
     * manual Bill
     *
     * @param  \App\Models\sms_history  $sms_history
     * @param  \App\Models\sms_gateway  $sms_gateway
     */
    public static function manualBill(sms_history $sms_history, sms_gateway $sms_gateway)
    {
        $sms_history->sms_count = SmsGatewayController::getSMSCount($sms_history->sms_body);
        $sms_history->sms_cost = $sms_history->sms_count  * $sms_gateway->unit_price;
        $sms_history->cost_checked = 1;
        $sms_history->save();
    }
}
