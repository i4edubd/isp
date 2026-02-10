<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;

class NagadPaymentGatewayBaseController extends Controller
{
    /**
     * Nagad Host
     *
     * @var string
     */
    protected $nagad_host = '';

    public function __construct()
    {
        if (config('local.is_sandbox_pgw')) {
            $this->nagad_host = 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs';
        } else {
            $this->nagad_host = 'https://api.mynagad.com/api/dfs';
        }
    }

    /**
     * Generate Random string
     *
     * @param int $length
     * @return string
     */
    public static function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Get clinet ip
     *
     * @return string
     */
    public static function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
