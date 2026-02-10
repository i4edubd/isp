<?php

use App\Http\Controllers\CacheController;
use App\Http\Controllers\SubscriptionFeeCalculator;
use App\Models\language;
use App\Models\operator;
use App\Models\vat_profile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;


if (!function_exists('isMenuActive')) {

    /**
     * Validate Mobile Number
     *
     * @param string  $menu
     * @param  App\Models\operator  $operator
     *
     * @return string mobile
     */
    function isMenuActive(string $menu, operator $operator)
    {
        $disabled_menus = CacheController::getDisabledMenus($operator);

        $disabled_count = $disabled_menus->where('menu', $menu)->count();

        return $disabled_count == 0 ? true : false;
    }
}

if (!function_exists('isMandatoryCustomerAttribute')) {

    /**
     * Mandatory Customer Attribute
     *
     * @param string  $attribute
     * @param  App\Models\operator  $operator
     *
     * @return string mobile
     */
    function isMandatoryCustomerAttribute(string $attribute, operator $operator)
    {
        $mandatory_attributes = CacheController::getMandatoryCustomerAttribute($operator);

        $attribute_count = $mandatory_attributes->where('attribute', $attribute)->count();

        return $attribute_count > 0 ? true : false;
    }
}

if (!function_exists('validate_mobile')) {

    /**
     * Validate Mobile Number
     *
     * @param string  $mobile_number
     * @param string  $country_code
     *
     * @return string mobile
     */
    function validate_mobile($mobile_number, $country_code = "")
    {
        if (!strlen($country_code)) {
            $country_code = config('consumer.country_code');
        }

        if (!strlen($mobile_number)) {
            return 0;
        }

        try {
            $first_char = substr($mobile_number, 0, 1);
            if ($first_char == "+") {
                $mobile_number = substr($mobile_number, 1);
            }
            return PhoneNumber::make($mobile_number, $country_code)->formatForMobileDialingInCountry($country_code);
        } catch (\Throwable $th) {
            Log::channel('validate_mobile')->debug('validate_mobile error.' . ' mobile_number: ' . $mobile_number . ' country_code: ' . $country_code . ' ' . $th);
            return 0;
        }
    }
}

if (!function_exists('getE164PhoneNumber')) {

    /**
     * Get E164 Formatted Mobile Number
     *
     * @param string  $mobile_number
     * @param string  $country_code
     *
     * @return string mobile
     */
    function getE164PhoneNumber($mobile_number, $country_code = "")
    {
        if (!strlen($country_code)) {
            $country_code = config('consumer.country_code');
        }

        $dialing_format = validate_mobile($mobile_number, $country_code);

        try {
            return PhoneNumber::make($dialing_format, $country_code)->formatE164();
        } catch (\Throwable $th) {
            Log::channel('validate_mobile')->debug('getE164PhoneNumber error.' . ' mobile_number: ' . $mobile_number . ' country_code: ' . $country_code . ' ' . $th);
            return 0;
        }
    }
}

if (!function_exists('getCountryCode')) {

    /**
     * Get iso2 Country Code
     *
     * @param int  $operator_id
     *
     * @return string iso2
     */
    function getCountryCode(int $operator_id)
    {
        $operator = CacheController::getOperator($operator_id);

        if (!$operator) {
            return config('consumer.country_code');
        }

        if (strlen($operator->country_id)) {
            $country = CacheController::getCountry($operator->country_id);
            if ($country) {
                return $country->iso2;
            }
        }

        return config('consumer.country_code');
    }
}

if (!function_exists('getCurrency')) {

    /**
     * Get currency code for the operator
     *
     * @param int  $operator_id
     *
     * @return string currency_code
     */
    function getCurrency(int $operator_id)
    {
        $operator = CacheController::getOperator($operator_id);

        if (!$operator) {
            return config('consumer.currency');
        }

        if (is_null($operator->country_id) == false) {
            $country = CacheController::getCountry($operator->country_id);
            if ($country) {
                return $country->currency_code;
            }
        }

        return config('consumer.currency');
    }
}


if (!function_exists('getTimeZone')) {

    /**
     * Get timezone for the operator
     *
     * @param int  $operator_id
     *
     * @return string timezone
     */
    function getTimeZone(int $operator_id)
    {
        $operator = CacheController::getOperator($operator_id);

        if (!$operator) {
            return config('app.timezone');
        }

        if (is_null($operator->timezone) == false) {
            return $operator->timezone;
        }

        return config('app.timezone');
    }
}

if (!function_exists('getLangCode')) {

    /**
     * Get lang_code for the operator
     *
     * @param int  $operator_id
     *
     * @return string lang_code
     */
    function getLangCode(int $operator_id)
    {
        $operator = CacheController::getOperator($operator_id);

        if (!$operator) {
            return config('consumer.lang_code');
        }

        if (is_null($operator->lang_code) == false) {
            return $operator->lang_code;
        }

        return config('consumer.lang_code');
    }
}

if (!function_exists('getLanguage')) {

    /**
     * Get lang_code for the operator
     *
     * @param  App\Models\operator  $operator
     * @return App\Models\language
     */
    function getLanguage(operator $operator)
    {
        $key = 'getLanguage_' . $operator->id;

        return Cache::remember($key, 300, function () use ($operator) {

            if (is_null($operator->lang_code)) {
                return language::where('code', 'en')->first();
            }

            return language::where('code', $operator->lang_code)->first();
        });
    }
}

if (!function_exists('getSubscriptionPrice')) {

    /**
     * Get lang_code for the operator
     *
     * @param int  $operator_id
     * @param int  $user_count
     *
     * @return string lang_code
     */
    function getSubscriptionPrice(int $operator_id, int $user_count)
    {
        $operator = CacheController::getOperator($operator_id);

        if (!$operator) {
            return 0;
        }

        return SubscriptionFeeCalculator::getSubscriptionFee($operator, getCurrency($operator_id), $user_count);
    }
}

if (!function_exists('currencyConversion')) {

    /**
     * Currency Conversion
     *
     * @param float  $amount
     * @param string  $currency
     *
     * @return Illuminate\Support\Collection
     */
    function currencyConversion(float $amount, string $currency)
    {
        $currency_group_bdt = ['BDT', 'INR', 'PKR', 'AFN', 'BTN', 'NPR', 'LKR'];

        if (in_array($currency, $currency_group_bdt)) {
            return collect(['amount' => $amount, 'currency_code' => $currency]);
        } else {
            return collect(['amount' => $amount / 100, 'currency_code' => 'USD']);
        }
    }
}

if (!function_exists('getSoftwareSupportNumber')) {

    /**
     * Software Support Number
     *
     * @param int  $operator_id
     *
     * @return ?string
     */
    function getSoftwareSupportNumber(int $operator_id)
    {
        if (config('consumer.help_menu') == false) {
            return false;
        }

        $group_admin = CacheController::getOperator($operator_id);

        if ($group_admin->role !== 'group_admin') {
            return false;
        }

        if ($group_admin->marketer_id > 0) {
            $marketer = CacheController::getOperator($group_admin->marketer_id);
            if ($marketer) {
                return $marketer->mobile;
            }
        }

        return config('consumer.helpline_number');
    }
}

if (!function_exists('getVatProfileDescription')) {

    /**
     * Vat Profile Description
     *
     * @param int  $vat_profile_id
     *
     * @return ?string
     */
    function getVatProfileDescription(int $vat_profile_id)
    {
        $key = 'vat_profile_' . $vat_profile_id;

        $ttl = 600;

        $vat_profile = Cache::remember($key, $ttl, function () use ($vat_profile_id) {

            return vat_profile::find($vat_profile_id);
        });

        if ($vat_profile) {
            return $vat_profile->description;
        } else {
            return false;
        }
    }
}

if (!function_exists('getLocaleString')) {

    /**
     * Get Locale String
     *
     * @param int  $operator_id
     * @param string  $string
     * @param int  $constants
     * @return ?string
     */
    function getLocaleString(int $operator_id, string $string, int $constants = 0)
    {
        // Take off and remember the litteral words.
        $literal_words = [];
        if ($constants > 0) {
            for ($i = 1; $i <= $constants; $i++) {
                $literal_words[$i] =  Str::between($string, "^$i", "$i^");
                $string = Str::replace("^$i" . $literal_words[$i] . "$i^", "^$i" . "$i^", $string);
            }
        }

        // conversion
        $lang_code = getLangCode($operator_id);
        $lang_file = lang_path($lang_code . '.json');
        if (file_exists($lang_file)) {
            $strings = json_decode(file_get_contents($lang_file), true);
            if (array_key_exists($string, $strings)) {
                $string = $strings[$string];
            }
        }

        // Remove placeholders and restore literal words
        if ($constants > 0) {
            for ($i = 1; $i <= $constants; $i++) {
                $string = Str::replace("^$i" . "$i^", $literal_words[$i], $string);
            }
        }

        // The converted string
        return $string;
    }
}

if (!function_exists('getAccountInfo')) {

    /**
     * get Account Info
     *
     * @param  \App\Models\operator  $operator
     * @return Illuminate\Support\Collection
     */
    function getAccountInfo(operator $operator)
    {
        $collection = [
            'title' => "Balance",
            'balance' => 'N/A',
            'account_type' => $operator->account_type,
            'url' => "#",
            'currency' => getCurrency($operator->id),
            'msg' => ""
        ];

        $roles = ['operator', 'sub_operator'];

        if (!in_array($operator->role, $roles)) {
            return collect($collection);
        }

        $account = CacheController::getResellerAccount($operator);

        if (!$account) {
            return collect($collection);
        }

        switch ($operator->account_type) {
            case 'debit':
                $collection['title'] = '';
                $collection['balance'] = $account->balance;
                $collection['url'] = route('accounts.OnlineRechage.create', ['account' => $account]);
                $collection['msg'] = 'Pay';
                return collect($collection);
                break;

            case 'credit':
                $collection['title'] = 'Dues';
                $collection['balance'] = $account->balance;
                $collection['url'] = route('accounts.OnlinePayment.create', ['account' => $account]);
                $collection['msg'] = 'Pay';
                return collect($collection);
                break;
        }

        return collect($collection);
    }
}

if (!function_exists('isMobileDevice')) {

    /**
     * Detect mobile device
     *
     * @return bool
     */
    function isMobileDevice()
    {
        $detect = new Mobile_Detect;

        return $detect->isMobile();
    }
}

if (!function_exists('sToHms')) {

    /**
     * Seconds to hour minute second
     *
     * @param int  $seconds
     *
     * @return string time
     */
    function sToHms(int $seconds)
    {

        $h = 0;
        $m = 0;
        $s = round($seconds);
        while ($s > 60) {
            $s = $s - 60;
            $m = $m + 1;
        }
        while ($m > 60) {
            $m = $m - 60;
            $h = $h + 1;
        }
        $value = $h . ' Hour ' . $m . ' Minute ' . $s . ' Second ';
        return $value;
    }
}

if (!function_exists('mToDhm')) {

    /**
     * Minutes to day hour minute
     *
     * @param int  $minutes
     *
     * @return string time
     */
    function mToDhm(int $minutes)
    {
        $d = 0;
        $h = 0;
        $m = round($minutes);

        while ($m >= 1440) {
            $m = $m - 1440;
            $d = $d + 1;
        }
        while ($m >= 60) {
            $m = $m - 60;
            $h = $h + 1;
        }
        $value = $d . ' Day ' . $h . ' Hour ' . $m . ' minute';
        return $value;
    }
}

if (!function_exists('getVarName')) {

    /**
     * Get Variable name from string
     *
     * @param string  $string
     *
     * @return string string
     */
    function getVarName(string $string)
    {

        $string = trim($string);
        $allowed_chars = ['_', '-', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        if (strlen($string)) {
            $var = '';
            $chars = str_split($string);
            $len = count($chars);
            for ($i = 0; $i < $len; $i++) {
                if (in_array($chars[$i], $allowed_chars)) {
                    $var .= $chars[$i];
                } else {
                    $var .= '_';
                }
            }
            return $var;
        } else {
            return '';
        }
    }
}


if (!function_exists('getUserName')) {

    /**
     * Get Variable name from string
     *
     * @param string  $string
     *
     * @return string string
     */
    function getUserName(string $string)
    {

        $string = trim($string);

        $allowed_chars = ['+', '@', '.', '_', '-', ':', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        if (strlen($string)) {
            $var = '';
            $chars = str_split($string);
            $len = count($chars);
            for ($i = 0; $i < $len; $i++) {
                if (in_array($chars[$i], $allowed_chars)) {
                    $var .= $chars[$i];
                }
            }
            return $var;
        } else {
            return null;
        }
    }
}


if (!function_exists('encryptOrDecrypt')) {
    /**
     * method to encrypt or decrypt a plain text string
     * initialization vector(IV) has to be the same when encrypting and decrypting
     *
     * @param string  $action : can be 'encrypt' or 'decrypt'
     * @param mixed   $data  : value to encrypt or decrypt
     * @return mixed value
     */
    function encryptOrDecrypt($action, $data)
    {
        $output = false;
        $cipher_method = "aes-128-cbc";
        $passphrase = "fPH15ofx1oA7L4OSpNRSCXUzoDbyvU3q";
        $iv_length = openssl_cipher_iv_length($cipher_method);
        $iv = substr(hash('sha256', $passphrase), 0, $iv_length);
        if ($action == 'encrypt') {
            $data  = json_encode($data);
            $output = openssl_encrypt($data, $cipher_method, $passphrase, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($data), $cipher_method, $passphrase, 0, $iv);
            $output = json_decode($output, true);
        }
        return $output;
    }
}
