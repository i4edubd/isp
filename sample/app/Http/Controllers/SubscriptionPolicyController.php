<?php

namespace App\Http\Controllers;

use App\Models\customer_count;
use App\Models\operator;
use Illuminate\Http\Request;

class SubscriptionPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admins.group_admin.subscription-policies');
    }


    /**
     * Calculate Subscription Bill Amount
     *
     * @param  \App\Models\operator  $operator
     * @return int
     */
    public static function getBillAmount(operator $operator)
    {

        $avg_coustomer = customer_count::where('mgid', $operator->id)->avg('customer_count');

        $user_count = round($avg_coustomer);

        // P#1 & P#2 | max 1000
        if ($user_count < 501) {

            $calculated_amount = round($user_count * 3);

            if ($calculated_amount < 500) {
                return 500;
            }

            if ($calculated_amount > 1000) {
                return 1000;
            }

            return $calculated_amount;
        }

        // P#3 (501-1000) | max 1500
        if ($user_count > 500 && $user_count < 1001) {

            $calculated_amount = round($user_count * 2);

            if ($calculated_amount > 1500) {
                return 1500;
            }

            return $calculated_amount;
        }

        // P#4 (1001-1700) | max 2000
        if ($user_count > 1000 && $user_count < 1701) {

            $calculated_amount = round($user_count * 1.5);

            if ($calculated_amount > 2000) {
                return 2000;
            }

            return $calculated_amount;
        }

        // P#5 (1701-3000) | max 3000
        if ($user_count > 1700 && $user_count < 3001) {

            $calculated_amount = round($user_count * 1.25);

            if ($calculated_amount > 3000) {
                return 3000;
            }

            return $calculated_amount;
        }

        // p#6
        return $user_count;
    }

    /**
     * Calculate Subscription Bill Amount
     *
     * @param  \App\Models\operator  $operator
     * @return int
     */
    public static function getCalculatedPrice(operator $operator)
    {

        $avg_coustomer = customer_count::where('mgid', $operator->id)->avg('customer_count');

        $user_count = round($avg_coustomer);

        // P#1 & P#2 | per user * 3
        if ($user_count < 501) {

            $calculated_amount = round($user_count * 3);

            if ($calculated_amount < 500) {
                return 500;
            }

            return $calculated_amount;
        }

        // P#3 (501-1000) | per user * 2
        if ($user_count > 500 && $user_count < 1001) {

            $calculated_amount = round($user_count * 2);

            return $calculated_amount;
        }

        // P#4 (1001-1700) | per user * 1.5
        if ($user_count > 1000 && $user_count < 1701) {

            $calculated_amount = round($user_count * 1.5);

            return $calculated_amount;
        }

        // P#5 (1701-3000) | per user * 1.25
        if ($user_count > 1700 && $user_count < 3001) {

            $calculated_amount = round($user_count * 1.25);

            return $calculated_amount;
        }

        // p#6 | per user * 1
        return $user_count;
    }
}
