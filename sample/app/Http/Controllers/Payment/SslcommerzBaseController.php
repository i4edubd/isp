<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;

class SslcommerzBaseController extends Controller
{
    /**
     * Payment URL
     *
     * @var string
     */
    protected $payment_url = '';

    /**
     * Validation URL
     *
     * @var string
     */
    protected $validation_url = '';

    /**
     * Re Check URL
     *
     * @var string
     */
    protected $recheck_url = '';

    public function __construct()

    {
        if (config('local.is_sandbox_pgw')) {
            $this->payment_url = 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php';
            $this->validation_url = 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?';
            $this->recheck_url = 'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php?';
        } else {
            $this->payment_url = 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
            $this->validation_url = 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php?';
            $this->recheck_url = 'https://securepay.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php?';
        }
    }
}
