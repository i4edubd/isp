<?php

namespace App\Http\Controllers;

use App\Models\customer_count;
use App\Models\operator;
use Illuminate\Support\Collection;

class SubscriptionFeeCalculator extends Controller
{

    /**
     * Subscription Fee
     *
     * @param  \App\Models\operator  $operator
     * @param  string|nullable 
     * @return \Illuminate\Support\Collection
     */
    public static function getSubscriptionFee(operator $operator, ?string $billing_currency = null, ?int $billable_users_count = 0): Collection
    {
        $avg_coustomer = customer_count::where('mgid', $operator->id)->avg('customer_count');

        $user_count = round($avg_coustomer);

        if ($billable_users_count > 0) {
            $user_count = $billable_users_count;
        }

        $currency = getCurrency($operator->id);

        if (strlen($billing_currency)) {
            $currency = $billing_currency;
        }

        // P#0
        if ($user_count == 0) {
            return collect(['per_user_fee' => 0, 'amount' => 0, 'calculated_price' => 0, 'currency_code' => $currency]);
        }

        // P#1 (1-10 user) | max 50
        if ($user_count < 11) {

            $calculated_price = round($user_count * 10);

            if ($calculated_price > 50) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 10, 'amount' => 50, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 10, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#2 (11-50 user) | max 200
        if ($user_count < 51) {

            $calculated_price = round($user_count * 5);

            if ($calculated_price > 200) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 5, 'amount' => 200, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return  self::feeAfterCurrencyConversion(collect(['per_user_fee' => 5, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#3 (51-170 user) | max 500
        if ($user_count < 171) {

            $calculated_price = round($user_count * 4);

            if ($calculated_price > 500) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 4, 'amount' => 500, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 4, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#4 (171-500 user) | max 1000
        if ($user_count < 501) {

            $calculated_price = round($user_count * 3);

            if ($calculated_price > 1000) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 3, 'amount' => 1000, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 3, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#5 (501-1000 user) | max 1500
        if ($user_count < 1001) {

            $calculated_price = round($user_count * 2);

            if ($calculated_price > 1500) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 2, 'amount' => 1500, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 2, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#6 (1001-1700 user) | max 2000
        if ($user_count < 1701) {

            $calculated_price = round($user_count * 1.5);

            if ($calculated_price > 2000) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 1.5, 'amount' => 2000, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 1.5, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#7 (1701-3000 user) | max 3000
        if ($user_count < 3001) {

            $calculated_price = round($user_count * 1.25);

            if ($calculated_price > 3000) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 1.25, 'amount' => 3000, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 1.25, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#8 (3001-4000 user) | max 3600
        if ($user_count < 4001) {

            $calculated_price = round($user_count * 1);

            if ($calculated_price > 3600) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 1, 'amount' => 3600, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 1, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#9 (4001-5000 user) | max 4000
        if ($user_count < 5001) {

            $calculated_price = round($user_count * 0.9);

            if ($calculated_price > 4000) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.9, 'amount' => 4000, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.9, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#10 (5001-6000 user) | max 4200
        if ($user_count < 6001) {

            $calculated_price = round($user_count * 0.8);

            if ($calculated_price > 4200) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.8, 'amount' => 4200, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.8, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#11 (6001-8000 user) | max 4800
        if ($user_count < 8001) {

            $calculated_price = round($user_count * 0.7);

            if ($calculated_price > 4800) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.7, 'amount' => 4800, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.7, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#12 (8001-10000 user) | max 5000
        if ($user_count < 10001) {

            $calculated_price = round($user_count * 0.6);

            if ($calculated_price > 5000) {
                return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.6, 'amount' => 5000, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
            }

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.6, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }

        // P#13 (10001-∞ user) | max ∞
        if ($user_count > 10000) {

            $calculated_price = round($user_count * 0.5);

            return self::feeAfterCurrencyConversion(collect(['per_user_fee' => 0.5, 'amount' => $calculated_price, 'calculated_price' => $calculated_price, 'currency_code' => $currency]), $currency);
        }
    }


    /**
     * Subscription Fee After Currency Conversion
     *
     * @param  \Illuminate\Support\Collection $fee
     * @param  string  $currency
     * @return \Illuminate\Support\Collection
     */
    public static function feeAfterCurrencyConversion(Collection $fee, string $currency): Collection
    {
        $currency_group_bdt = ['BDT', 'INR', 'PKR', 'AFN', 'BTN', 'NPR', 'LKR'];

        if (in_array($currency, $currency_group_bdt) == false && $fee->get('amount') < 200) {
            return collect(['per_user_fee' => 5 / 100, 'amount' => 2, 'calculated_price' => 2, 'currency_code' => 'USD']);
        }

        if (in_array($currency, $currency_group_bdt)) {
            return $fee;
        } else {
            return collect(['per_user_fee' => (float)$fee->get('per_user_fee') / 100, 'amount' => round((int)$fee->get('amount') / 100), 'calculated_price' => round((int)($fee->get('calculated_price'))), 'currency_code' => 'USD']);
        }
    }
}
