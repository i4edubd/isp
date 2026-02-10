<?php

namespace App\Services\Payments;

use Illuminate\Support\Manager;
use InvalidArgumentException;

class PaymentService extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('payment-gateways.default');
    }

    /**
     * Create an instance of the SSLCommerz driver.
     *
     * @return Providers\SslCommerzProvider
     */
    public function createSslcommerzDriver()
    {
        $config = $this->config->get('payment-gateways.providers.sslcommerz');
        return new Providers\SslCommerzProvider($config);
    }

    /**
     * Create an instance of the Recharge Card driver.
     *
     * @return Providers\RechargeCardProvider
     */
    public function createRechargeCardDriver()
    {
        $config = $this->config->get('payment-gateways.providers.recharge_card');
        return new Providers\RechargeCardProvider($config);
    }
    
    // You would add more create<DriverName>Driver methods here for bKash, Nagad, etc.
}
