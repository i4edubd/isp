<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\payment_gateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

abstract class bKashTokenizedAbstractController extends Controller
{
    /**
     * Base URL
     *
     * @var string
     */
    public $base_URL = '';

    /**
     * API Version
     *
     * @var string
     */
    public $api_version  = 'v1.2.0-beta';

    /**
     * Grant Token Path
     *
     * @var string
     */
    public $grant_token_path  = '/tokenized/checkout/token/grant';

    /**
     * Refresh Token Path
     *
     * @var string
     */
    public $refresh_token_path = '/tokenized/checkout/token/refresh';

    /**
     * Create Agreement Path
     *
     * @var string
     */
    public $create_agreement_path = '/tokenized/checkout/create';

    /**
     * Execute Agreement Path
     *
     * @var string
     */
    public $execute_agreement_path = '/tokenized/checkout/execute';

    /**
     * Query Agreement Path
     *
     * @var string
     */
    public $query_agreement_path = '/tokenized/checkout/agreement/status';

    /**
     * Cancel Agreement Path
     *
     * @var string
     */
    public $cancel_agreement_path = '/tokenized/checkout/agreement/cancel';

    /**
     * Create Payment Path
     *
     * @var string
     */
    public $create_payment_path = '/tokenized/checkout/create';

    /**
     * Execute Payment Path
     *
     * @var string
     */
    public $execute_payment_path = '/tokenized/checkout/execute';

    /**
     * Query Payment Path
     *
     * @var string
     */
    public $query_payment_path = '/tokenized/checkout/payment/status';

    /**
     * Search Payment Path
     *
     * @var string
     */
    public $search_transaction_path = '/tokenized/checkout/general/searchTransaction';

    /**
     * Refund Transaction Path
     *
     * @var string
     */
    public $refund_transaction_path = '/tokenized/checkout/payment/refund';

    /**
     * Refund Status Path
     *
     * @var string
     */
    public $refund_status_path = '/tokenized/checkout/payment/refund';

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
     * Create Agreement URL
     *
     * @var string
     */
    public $create_agreement_url = '';

    /**
     * Execute Agreement URL
     *
     * @var string
     */
    public $execute_agreement_url = '';

    /**
     * Query Agreement URL
     *
     * @var string
     */
    public $query_agreement_url = '';

    /**
     * Cancel Agreement URL
     *
     * @var string
     */
    public $cancel_agreement_url = '';

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
     * Refund Transaction URL
     *
     * @var string
     */
    public $refund_transaction_url = '';

    /**
     * Refund Status URL
     *
     * @var string
     */
    public $refund_status_url = '';

    /**
     * Log Response
     *
     * @var bool
     */
    public $log_response = false;

    public function __construct()
    {
        if (config('local.is_sandbox_pgw')) {
            $this->base_URL = 'https://tokenized.sandbox.bka.sh/';
            $this->log_response = true;
        } else {
            $this->base_URL = 'https://tokenized.pay.bka.sh/';
            $this->log_response = false;
        }

        $this->grant_token_url = $this->base_URL . $this->api_version .  $this->grant_token_path;

        $this->refresh_token_url = $this->base_URL . $this->api_version . $this->refresh_token_path;

        $this->create_agreement_url = $this->base_URL . $this->api_version . $this->create_agreement_path;

        $this->execute_agreement_url = $this->base_URL . $this->api_version . $this->execute_agreement_path;

        $this->query_agreement_url = $this->base_URL . $this->api_version . $this->query_agreement_path;

        $this->cancel_agreement_url = $this->base_URL . $this->api_version . $this->cancel_agreement_path;

        $this->create_payment_url = $this->base_URL . $this->api_version .  $this->create_payment_path;

        $this->execute_payment_url =  $this->base_URL . $this->api_version .  $this->execute_payment_path;

        $this->query_payment_url =  $this->base_URL . $this->api_version .  $this->query_payment_path;

        $this->search_transaction_url = $this->base_URL . $this->api_version .  $this->search_transaction_path;

        $this->refund_transaction_url = $this->base_URL . $this->api_version . $this->refund_transaction_path;

        $this->refund_status_url = $this->base_URL . $this->api_version . $this->refund_status_path;
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
     * @param \App\Models\customer_payment $customer_payment
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
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'username' => $payment_gateway->username,
            'password' => $payment_gateway->password,
        ])->post($this->grant_token_url, [
            'app_key' => $payment_gateway->app_key,
            'app_secret' => $payment_gateway->app_secret,
        ]);

        if (config('consumer.debug_payment')) {
            Storage::put('bkash_tokenized_debug/grantToken.json', $response);
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
