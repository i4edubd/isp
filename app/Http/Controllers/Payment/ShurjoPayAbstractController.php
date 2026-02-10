<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\payment_gateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

abstract class ShurjoPayAbstractController extends Controller
{
    /**
     * Base URL
     *
     * @var string
     */
    public $base_URL = '';

    /**
     * Get Token Path
     *
     * @var string
     */
    public $get_token_path  = '/api/get_token';

    /**
     * Secret Pay Path
     *
     * @var string
     */
    public $secret_pay_path  = '/api/secret-pay';

    /**
     * Verification Path
     *
     * @var string
     */
    public $verification_path = '/api/verification';

    /**
     * Get Token URL
     *
     * @var string
     */
    public $get_token_url  = '';

    /**
     * Secret Pay URL
     *
     * @var string
     */
    public $secret_pay_url  = '';

    /**
     * Verification URL
     *
     * @var string
     */
    public $verification_url  = '';

    /**
     * Log Response
     *
     * @var bool
     */
    public $log_response = false;

    public function __construct()
    {
        if (config('local.is_sandbox_pgw')) {
            $this->base_URL = 'https://sandbox.shurjopayment.com';
            $this->log_response = true;
        } else {
            $this->base_URL = 'https://engine.shurjopayment.com';
            $this->log_response = false;
        }

        $this->get_token_url = $this->base_URL .  $this->get_token_path;

        $this->secret_pay_url = $this->base_URL . $this->secret_pay_path;

        $this->verification_url = $this->base_URL . $this->verification_path;
    }

    /**
     * Get Payment Gateway
     *
     * @param int $id
     *
     * @return \App\Models\payment_gateway
     */
    public function getPaymentGateway(int $id)
    {
        $key = 'payment_gateway_' . $id;

        $ttl = 600;

        return Cache::remember($key, $ttl, function () use ($id) {
            return payment_gateway::findOrFail($id);
        });
    }

    /**
     * Get Token path for ShurjoPay
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */
    public function getTokenStoragePath(payment_gateway $payment_gateway)
    {
        return 'ShurjoPay/tokens/' . $payment_gateway->id . '.json';
    }

    /**
     * Get Token for Bkash Checkout
     *
     * @param \App\Models\customer_payment $customer_payment
     *
     * @return \Illuminate\Http\Response
     */

    public function  getToken(payment_gateway $payment_gateway)
    {
        $storage_tokens = Storage::path($this->getTokenStoragePath($payment_gateway));

        if (file_exists($storage_tokens)) {
            $tokens = json_decode(file_get_contents($storage_tokens), true);
            if (array_key_exists('expire_at', $tokens)) {
                $now = Carbon::now(config('app.timezone'));
                $expire_at = Carbon::create($tokens['expire_at']);
                if ($expire_at->greaterThan($now)) {
                    return 0;
                }
            }
        }

        $response = Http::withHeaders([
            'Content-Type: application/json'
        ])->post($this->get_token_url, [
            'username' => $payment_gateway->username,
            'password' => $payment_gateway->password,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('ShurjoPay_debug/getToken.json', $response);
        }

        $tokens = json_decode($response, true);

        if (is_array($tokens) == false) {
            abort(500, 'Gateway Error');
        }

        if (array_key_exists('token', $tokens)) {
            $now = Carbon::now(config('app.timezone'));
            $expire_at = $now->addMilliseconds($tokens['expires_in']);
            $tokens['expire_at'] = $expire_at;
            $new_tokens = json_encode($tokens);
            Storage::put($this->getTokenStoragePath($payment_gateway), $new_tokens);
        } else {
            abort(500, 'Token Generation Failed');
        }
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
