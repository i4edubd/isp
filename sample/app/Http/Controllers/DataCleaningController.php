<?php

namespace App\Http\Controllers;

use App\Models\card_distributor_payments;
use App\Models\cash_in;
use App\Models\cash_out;
use App\Models\customer_bill;
use App\Models\customer_complain;
use App\Models\customer_count;
use App\Models\customer_import_report;
use App\Models\customer_import_request;
use App\Models\customer_payment;
use App\Models\expense;
use App\Models\Mikrotik\mikrotik_ip_pool;
use App\Models\Mikrotik\mikrotik_ppp_profile;
use App\Models\Mikrotik\mikrotik_ppp_secret;
use App\Models\operators_income;
use App\Models\sms_history;
use App\Models\sms_payment;
use App\Models\subscription_payment;
use Illuminate\Http\Request;

class DataCleaningController extends Controller
{

    /**
     * Monthly Data Cleaning.
     *
     * @return int
     */
    public static function monthly()
    {

        // customer_import_requests
        $import_requests = customer_import_request::where('status', 'done')->get();

        foreach ($import_requests as $import_request) {
            customer_import_report::where('request_id', $import_request->id)->delete();
            mikrotik_ip_pool::where('customer_import_request_id', $import_request->id)->delete();
            mikrotik_ppp_profile::where('customer_import_request_id', $import_request->id)->delete();
            mikrotik_ppp_secret::where('customer_import_request_id', $import_request->id)->delete();
            $import_request->delete();
        }

        return 0;
    }


    /**
     * Yearly data cleaning.
     *
     * @return int
     */
    public static function yearly()
    {

        $cleaning_year = date(config('app.year_format')) - 1;

        $cleaning_month = date(config('app.month_format'));

        $where = [
            ['year', '=', $cleaning_year],
            ['month', '=', $cleaning_month],
        ];

        // card_distributor_payments
        card_distributor_payments::where($where)->delete();

        // cash_ins
        cash_in::where($where)->delete();

        // cash_out
        cash_out::where($where)->delete();

        // customer_bills
        customer_bill::where($where)->delete();

        // customer_counts
        customer_count::where($where)->delete();

        // customer_payments
        customer_payment::where($where)->delete();

        // sms_histories
        sms_history::where($where)->delete();

        // customer_complains
        $customer_complains = customer_complain::where($where)->get();
        foreach ($customer_complains as $customer_complain) {
            $customer_complain->delete();
        }

        return 0;
    }


    /**
     * Biyearly data cleaning.
     *
     * @return int
     */
    public static function biyearly()
    {

        $cleaning_year = date(config('app.year_format')) - 2;

        $cleaning_month = date(config('app.month_format'));

        $where = [
            ['year', '=', $cleaning_year],
            ['month', '=', $cleaning_month],
        ];

        // expenses
        expense::where($where)->delete();

        // operators_incomes
        operators_income::where($where)->delete();

        // sms_payments
        sms_payment::where($where)->delete();

        // subscription_payments
        subscription_payment::where($where)->delete();

        return 0;
    }
}
