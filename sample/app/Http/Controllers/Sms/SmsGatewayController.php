<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\country;
use App\Models\minimum_sms_bill;
use App\Models\operator;
use App\Models\sms_balance_history;
use App\Models\sms_gateway;
use App\Models\sms_history;
use App\Models\sms_bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Instasent\SMSCounter\SMSCounter;

class SmsGatewayController extends Controller
{
    /**
     * Get the SMS Gateway for the operator
     *
     * @param  \App\Models\operator  $operator
     * @return  \App\Models\sms_gateway
     */

    public static function getSmsGw(operator $operator)
    {
        // << pay your dues first
        $has_bill = sms_bill::where('operator_id', $operator->id)->count();

        if ($has_bill) {
            return sms_gateway::make([
                'id' => 0,
                'operator_id' => 0,
                'provider_name' => 'Demo',
                'unit_price' => '0.0',
                'from_number' => '01751000000',
            ]);
        }
        // >>

        $cache_key = 'sms_gw_' . $operator->id;

        $ttl = 600;

        return Cache::remember($cache_key, $ttl, function () use ($operator) {

            $country_code = getCountryCode($operator->id);

            //use own Gateway
            $count = sms_gateway::where('operator_id', $operator->id)->count();
            if ($count) {
                return sms_gateway::where('operator_id', $operator->id)->first();
            }

            //use group admin's gw
            $where = [
                ['operator_id', '=', $operator->gid],
                ['country_code', '=', $country_code],
                ['saleable', '=', 1],
            ];
            $count = sms_gateway::where($where)->count();
            if ($count) {
                return sms_gateway::where($where)->first();
            }

            //use master admin's gw
            $where = [
                ['operator_id', '=', $operator->mgid],
                ['country_code', '=', $country_code],
                ['saleable', '=', 1],
            ];
            $count = sms_gateway::where($where)->count();
            if ($count) {
                return sms_gateway::where($where)->first();
            }

            //use Super Admin's gw
            $where = [
                ['operator_id', '=', $operator->sid],
                ['country_code', '=', $country_code],
                ['saleable', '=', 1],
            ];
            $count = sms_gateway::where($where)->count();
            if ($count) {
                return sms_gateway::where($where)->first();
            }

            return sms_gateway::make([
                'id' => 0,
                'operator_id' => 0,
                'provider_name' => 'Demo',
                'unit_price' => '0.0',
                'from_number' => '01751000000',
            ]);
        });
    }

    /**
     * send sms to number
     *
     * @param  \App\Models\operator  $operator
     * @param string $mobile_number
     * @param string $message
     * @param int $customer_id
     *
     * @return mixed
     */

    public static function sendSms(operator $operator, string $mobile_number, string $message, int $customer_id = 0)
    {

        // Disabled Messages
        if (strlen($message) < 5) {
            return 0;
        }

        //sms gw
        $sms_gateway = self::getSmsGw($operator);

        //sms_history
        $sms_history = new sms_history();
        $sms_history->operator_id = $operator->id;
        $sms_history->customer_id = $customer_id;
        $sms_history->sms_gateway_id = $sms_gateway->id;
        $sms_history->from_number = $sms_gateway->from_number;
        $sms_history->to_number = $mobile_number;
        $sms_history->status_text = 'Pending';
        $sms_history->sms_body = $message;
        $sms_history->date = date(config('app.date_format'));
        $sms_history->week = date(config('app.week_format'));
        $sms_history->month = date(config('app.month_format'));
        $sms_history->year = date(config('app.year_format'));
        $sms_history->save();

        // demo sms gateway
        if ($sms_gateway->id == 0) {
            return $sms_history->id;
        }

        // demo mgid
        if ($operator->mgid == config('consumer.demo_gid')) {
            $sms_history->status_text = 'Successful';
            $sms_history->save();
            return $sms_history->id;
        }

        //send sms
        switch ($sms_gateway->provider_name) {
            case 'robi':
                $api_controller = new RobiSmsApiController();
                $api_controller->sendSms($sms_history);
                break;

            case 'bangladeshsms':
            case 'm2mbd':
            case 'maestro':
            case 'btssms':
            case '880sms':
            case 'elitbuzz':
            case 'brandsms':
            case 'metrotel':
            case 'dianahost':
            case 'dhakasoftbd':
                $api_controller = new ElitbuzzFamilySMSController();
                $api_controller->sendSms($sms_history);
                break;

            case 'bulksmsbd':
                $api_controller = new BulksmsbdApiController();
                $api_controller->sendSms($sms_history);
                break;

            case 'bdsmartpay':
                $api_controller = new BdSmartPaySmsController();
                $api_controller->sendSms($sms_history);
                break;

            case 'sslwireless':
                $api_controller = new SslwirelessSmsGatewayController();
                $api_controller->sendSms($sms_history);
                break;

            case 'adnsms':
                $api_controller = new AdnSmsController();
                $api_controller->sendSms($sms_history);
                break;

            case '24smsbd':
                $api_controller = new TwentyFourSmsBdApiController();
                $api_controller->sendSms($sms_history);
                break;

            case 'smsnet':
                $api_controller = new SmsnetSmsGatewayController();
                $api_controller->sendSms($sms_history);
                break;

            case 'smsinbd':
                $api_controller = new SmsinbdController();
                $api_controller->sendSms($sms_history);
                break;
        }

        return $sms_history->id;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->role == 'developer') {
            $sms_gateways = sms_gateway::all();
            return view('admins.developer.sms-gateways', [
                'sms_gateways' => $sms_gateways,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->user()->role == 'developer') {
            $operators = operator::all();
            $countries = country::whereIn('iso2', config('country.allowed_countries'))->get();
            return view('admins.developer.sms-gateway-create', [
                'operators' => $operators,
                'countries' => $countries,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cache_key = 'sms_gw_' . $request->operator_id;
        if (Cache::has($cache_key)) {
            Cache::forget($cache_key);
        }
        $country = country::findOrFail($request->country_id);
        $sms_gateway = new sms_gateway();
        $sms_gateway->operator_id = $request->operator_id;
        $sms_gateway->country_code = $country->iso2;
        $sms_gateway->provider_name = $request->provider_name;
        $sms_gateway->token = $request->token;
        $sms_gateway->username = $request->username;
        $sms_gateway->email = $request->email;
        $sms_gateway->password = $request->password;
        $sms_gateway->from_number = $request->from_number;
        $sms_gateway->post_url = $request->post_url;
        $sms_gateway->delivery_report_url = $request->delivery_report_url;
        $sms_gateway->balance_check_url = $request->balance_check_url;
        $sms_gateway->unit_price = $request->unit_price;
        $sms_gateway->saleable = $request->saleable;
        $sms_gateway->save();
        return redirect()->route('sms_gateways.index')->with('success', 'SMS gateway has been added successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\sms_gateway  $sms_gateway
     * @return \Illuminate\Http\Response
     */
    public function edit(sms_gateway $sms_gateway)
    {
        return view('admins.developer.sms-gateway-edit', [
            'sms_gateway' => $sms_gateway,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\sms_gateway  $sms_gateway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, sms_gateway $sms_gateway)
    {
        $cache_key = 'sms_gw_' . $sms_gateway->operator_id;
        if (Cache::has($cache_key)) {
            Cache::forget($cache_key);
        }
        $sms_gateway->token = $request->token;
        $sms_gateway->username = $request->username;
        $sms_gateway->password = $request->password;
        $sms_gateway->email = $request->email;
        $sms_gateway->from_number = $request->from_number;
        $sms_gateway->post_url = $request->post_url;
        $sms_gateway->delivery_report_url = $request->delivery_report_url;
        $sms_gateway->balance_check_url = $request->balance_check_url;
        $sms_gateway->unit_price = $request->unit_price;
        $sms_gateway->saleable = $request->saleable;
        $sms_gateway->save();

        return redirect()->route('sms_gateways.index')->with('success', 'SMS Gateway has been updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\sms_gateway  $sms_gateway
     * @return \Illuminate\Http\Response
     */
    public function destroy(sms_gateway $sms_gateway)
    {
        $cache_key = 'sms_gw_' . $sms_gateway->operator_id;
        if (Cache::has($cache_key)) {
            Cache::forget($cache_key);
        }
        $sms_gateway->delete();
        return redirect()->route('sms_gateways.index')->with('success', 'SMS Gateway has been deleted successfully');
    }

    /**
     * Generate SMS Bill
     */
    public function generateBill()
    {
        // remove failed sms histories
        sms_history::where('status_text', '!=', 'ok')
            ->where('created_at', '<', now()->subDay())
            ->delete();

        //Get saleable sms gateways
        $sms_gateways = sms_gateway::where('saleable', 1)->get();

        foreach ($sms_gateways as $sms_gateway) {

            $where = [];

            #1 consider sms gateway
            $where[0] = ['sms_gateway_id', '=', $sms_gateway->id];

            #2 consider unbilled sms
            $where[1] = ['sms_bill_id', '=', 0];

            $operators = operator::all();

            foreach ($operators as $operator) {

                //Exclude Merchant
                if ($operator->id == $sms_gateway->operator_id) {
                    $where[2] = ['operator_id', '=', $operator->id];
                    $where[3] = ['status_text', '=', 'Successful'];
                    sms_history::where($where)->update(['sms_bill_id' => 1, 'cost_checked' => 1]);
                    continue;
                }

                #3 consider operator to bill
                $where[2] = ['operator_id', '=', $operator->id];

                #4 Consider Only Successful messages
                $where[3] = ['status_text', '!=', 'Ok'];

                #5 Consider the messages, cost to be checked
                $where[4] =  ['cost_checked', '=', 0];

                $sms_histories = sms_history::where($where)->get();

                // recheck cost
                switch ($sms_gateway->provider_name) {
                    case 'bangladeshsms':
                    case 'm2mbd':
                    case 'maestro':
                    case 'btssms':
                    case '880sms':
                    case 'elitbuzz':
                    case 'brandsms':
                    case 'metrotel':
                    case 'dianahost':
                    case 'dhakasoftbd':
                        foreach ($sms_histories as $sms_history) {
                            ElitbuzzFamilySMSController::checkCost($sms_history, $sms_gateway);
                        }
                        break;
                }

                #5 Consider messages for billing.
                $where[4] =  ['cost_checked', '=', 1];

                //amount
                $amount = sms_history::where($where)->sum('sms_cost');

                // billable amount
                $minimum_bill = minimum_sms_bill::where('operator_id', $operator->id)->firstOr(function () {
                    return
                        minimum_sms_bill::make([
                            'id' => 0,
                            'operator_id' => 0,
                            'amount' => config('consumer.minimum_sms_bill'),
                        ]);
                });

                if ($amount > $minimum_bill->amount) {

                    $sms_count = sms_history::where($where)->sum('sms_count');

                    $from_date = sms_history::where($where)->min('created_at');

                    $to_date = sms_history::where($where)->max('created_at');

                    $service_charge = $amount * (10 / 100);

                    $amount = $amount + $service_charge;

                    //Generate Bill
                    $sms_bill = new sms_bill();
                    $sms_bill->operator_id = $operator->id;
                    $sms_bill->merchant_id = $sms_gateway->operator_id;
                    $sms_bill->sms_count = $sms_count;
                    $sms_bill->sms_cost = round($amount);
                    $sms_bill->from_date = $from_date;
                    $sms_bill->to_date = $to_date;
                    $sms_bill->month = date(config('app.month_format'));
                    $sms_bill->year = date(config('app.year_format'));
                    $sms_bill->due_date = date(config('app.date_format'));
                    $sms_bill->save();

                    //mark sms histories as billed
                    $sms_histories = sms_history::where($where)->get();
                    foreach ($sms_histories as $sms_history) {
                        $sms_history->sms_bill_id = $sms_bill->id;
                        $sms_history->save();
                    }

                    // make payment from balance
                    if ($operator->sms_balance >= $sms_bill->sms_cost) {
                        $old_balance = $operator->sms_balance;
                        $new_balance = $operator->sms_balance - $sms_bill->sms_cost;
                        $operator->sms_balance = $new_balance;
                        $operator->save();
                        sms_balance_history::create([
                            'operator_id' => $operator->id,
                            'type' => 'out',
                            'sms_bill_id' => $sms_bill->id,
                            'amount' => $sms_bill->sms_cost,
                            'old_balance' => $old_balance,
                            'new_balance' => $new_balance,
                        ]);
                        $sms_bill->delete();
                    }
                }
            }
        }
    }

    /**
     * Get SMS Count
     *
     * @param  string $sms_body
     */
    public static function getSMSCount(string $sms_body): int
    {
        $smsCounter = new SMSCounter();
        $object = $smsCounter->count($sms_body);
        return $object->messages;
    }
}
