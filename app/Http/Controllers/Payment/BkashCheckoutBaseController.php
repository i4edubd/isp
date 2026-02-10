<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\payment_gateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BkashCheckoutBaseController extends Controller
{
    /**
     * Grant Token Path
     *
     * @var string
     */
    public $grant_token_path  = '/checkout/token/grant';

    /**
     * Refresh Token Path
     *
     * @var string
     */
    public $refresh_token_path = '/checkout/token/refresh';

    /**
     * Create Payment Path
     *
     * @var string
     */
    public $create_payment_path = '/checkout/payment/create';

    /**
     * Execute Payment Path
     *
     * @var string
     */
    public $execute_payment_path = '/checkout/payment/execute/';

    /**
     * Query Payment Path
     *
     * @var string
     */
    public $query_payment_path = '/checkout/payment/query/';

    /**
     * Search Payment Path
     *
     * @var string
     */
    public $search_transaction_path = '/checkout/payment/search/';

    /**
     * Bkash Script URL
     *
     * @var string
     */
    public $bkash_script_url = '';

    /**
     * Base URL
     *
     * @var string
     */
    public $base_URL = '';

    /**
     * Grant Token URL
     *
     * @var string
     */
    public $grant_token_url = '';

    /**
     * Refresh Token URL
     *
     * @var string
     */
    public $refresh_token_url = '';

    /**
     * Create Payment URL
     *
     * @var string
     */

    public $create_payment_url = '';

    /**
     * Execute Payment URL
     *
     * @var string
     */
    public $execute_payment_url = '';

    /**
     * Query Payment URL
     *
     * @var string
     */
    public $query_payment_url = '';

    /**
     * Search Payment URL
     *
     * @var string
     */
    public $search_transaction_url = '';

    /**
     * Log Response
     *
     * @var bool
     */
    public $log_response = false;

    public function __construct()

    {
        if (config('local.is_sandbox_pgw')) {
            $this->base_URL = 'https://checkout.sandbox.bka.sh/v1.2.0-beta';
            $this->bkash_script_url = 'https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js';
            $this->log_response = true;
        } else {
            $this->base_URL = 'https://checkout.pay.bka.sh/v1.2.0-beta';
            $this->bkash_script_url = 'https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js';
            $this->log_response = false;
        }

        $this->grant_token_url = $this->base_URL . $this->grant_token_path;

        $this->refresh_token_url = $this->base_URL . $this->refresh_token_path;

        $this->create_payment_url = $this->base_URL . $this->create_payment_path;

        $this->execute_payment_url =  $this->base_URL . $this->execute_payment_path;

        $this->query_payment_url =  $this->base_URL . $this->query_payment_path;

        $this->search_transaction_url = $this->base_URL . $this->search_transaction_path;
    }

    /**
     * Generate Bkash Payment Token
     *
     * @return string
     */
    public function token()
    {
        $bytes = random_bytes(4);
        return bin2hex($bytes);
    }

    /**
     * Get Credentials File Name
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return string
     */
    public function getCredentialsFileName(payment_gateway $payment_gateway)
    {
        $credentials_path = storage_path('payment-gateways/');
        if (file_exists($credentials_path) == false) {
            mkdir($credentials_path);
        }
        $file_name = $payment_gateway->id . $payment_gateway->provider_name . $payment_gateway->operator_id . '.json';
        $credentials_file = $credentials_path . $file_name;
        return $credentials_file;
    }

    /**
     * Get Token for Bkash Checkout
     *
     * @param \App\Models\payment_gateway $payment_gateway
     *
     * @return \Illuminate\Http\Response
     */

    public function  grantToken(payment_gateway $payment_gateway)
    {

        // function variable
        $credentials = [];

        $credentials_file = $this->getCredentialsFileName($payment_gateway);

        if (file_exists($credentials_file)) {
            $credentials = json_decode(file_get_contents($credentials_file), true);
            if (array_key_exists('expire_at', $credentials)) {
                $now = Carbon::now(config('app.timezone'));
                $expire_at = Carbon::create($credentials['expire_at']);
                if ($expire_at->greaterThan($now)) {
                    return 0;
                }
            }
        }

        $response = Http::timeout(30)->withHeaders([
            'accept' => 'application/json',
            'username' => $payment_gateway->username,
            'password' => $payment_gateway->password,
            'content-type' => 'application/json',
        ])->post($this->grant_token_url, [
            'app_key' => $payment_gateway->app_key,
            'app_secret' => $payment_gateway->app_secret,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_debug/grantToken.json', $response);
        }

        $tokens = json_decode($response, true);

        if (array_key_exists('id_token', $tokens) == false) {
            abort(500, json_encode($tokens));
        }

        $idtoken = $tokens['id_token'];

        $credentials['token'] = $idtoken;

        $now = Carbon::now(config('app.timezone'));
        $expire_at = $now->addSeconds(1800);
        $credentials['expire_at'] = $expire_at;

        $credentials['username'] = $payment_gateway->username;
        $credentials['password'] = $payment_gateway->password;
        $credentials['app_key'] = $payment_gateway->app_key;
        $credentials['app_secret'] = $payment_gateway->app_secret;

        $newcredentials = json_encode($credentials);

        file_put_contents($credentials_file, $newcredentials);

        return 1;
    }
}
