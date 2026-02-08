<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\bKashTokenizedCustomerPaymentController;
use App\Models\customer_count;
use App\Models\customer_payment;
use App\Models\event_sms;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{


    public function setHour()
    {
        $now = Carbon::now()->setHour(12);

        $nowp = Carbon::now()->setHour(0)->setMinute(0)->setSecond(0);

        echo $nowp;

        if ($now->lessThan($nowp)) {
            echo "true";
        }
    }

    public function generateSms()
    {

        $operator = Auth::user();

        $paymet_date = date(config('app.date_format'));

        $customer_count = 20;

        $customer = customer::find(1);

        return SmsGenerator::welcomeMsgPpp($operator, $customer);
    }

    public function timestamp()
    {
        $now = Carbon::now()->timestamp;
        echo $now;
    }


    public function trialPackage()
    {
        $operator = operator::find(3);
        $p = PackageController::trialPackage($operator);

        dump($p);
    }


    public function dateAddDiff()
    {
        $now = CarbonImmutable::now(config('app.timezone'));

        $tomorrow = $now->addDays(10);

        echo $now;

        echo "<br>";

        echo $tomorrow;

        $diff = $now->diffInDays($tomorrow);

        echo "<br>";

        echo $diff;

        $diff = $tomorrow->diffInDays($now);

        echo "<br>";

        echo $diff;
    }

    public function package()
    {

        $package = package::find(66);

        return $package->master_package;
    }

    public function bKash()
    {
        $customer_payment = customer_payment::find(43);

        $controller = new bKashTokenizedCustomerPaymentController();

        return $controller->searchTransaction($customer_payment);
    }

    public function explode()
    {
        $text = "23:192.168.1.1";

        $array = explode("--", $text);

        dump($array);

        return count($array);
    }

    public function hasAttribute()
    {

        $operator_id = 0;

        $event_sms = event_sms::where('event', 'SEND_MONEY_NOTIFICATION')
            ->where('operator_id', $operator_id)
            ->firstOr(function () {
                return event_sms::where('event', 'SEND_MONEY_NOTIFICATION')
                    ->where('operator_id', 0)
                    ->first();
            });

        $lang = 'bn';

        $sms_coloum = 'default_sms_bn';

        if ($event_sms->getAttributeValue($sms_coloum)) {
            dump($event_sms->getAttributeValue($sms_coloum));
            return 'true';
        } else {
            dump($event_sms->getAttributeValue($sms_coloum));
            return 'false';
        }
    }


    public function e164()
    {

        $mobile = '01751045781';

        return getE164PhoneNumber($mobile);
    }


    public function testFee()
    {

        $operator = operator::find(24);

        return SubscriptionFeeCalculator::getSubscriptionFee($operator, 'USD');

    }
}
