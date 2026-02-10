<?php

namespace App\Services\Sms;

use App\Services\Sms\Providers\SmsProviderInterface;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class SmsService
{
    protected $provider;

    public function __construct()
    {
        $defaultProvider = config('sms-gateways.default');
        $this->provider = $this->resolveProvider($defaultProvider);
    }

    /**
     * Set the SMS provider to use.
     *
     * @param string $provider
     * @return self
     */
    public function provider(string $provider): self
    {
        $this->provider = $this->resolveProvider($provider);
        return $this;
    }

    /**
     * Send the SMS message.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        try {
            return $this->provider->send($to, $message);
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            // Here you could implement fallback logic if needed.
            return false;
        }
    }

    /**
     * Resolve the given provider.
     *
     * @param string $provider
     * @return SmsProviderInterface
     */
    protected function resolveProvider(string $provider): SmsProviderInterface
    {
        $config = config("sms-gateways.providers.{$provider}");

        if (!$config) {
            throw new InvalidArgumentException("SMS provider [{$provider}] is not configured.");
        }

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->$driverMethod($config);
        }

        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    /**
     * Create an instance of the Maestro driver.
     *
     * @param array $config
     * @return Providers\MaestroSmsProvider
     */
    protected function createMaestroDriver(array $config)
    {
        return new Providers\MaestroSmsProvider($config);
    }
    
    /**
     * Create an instance of the Log driver.
     *
     * @param array $config
     * @return Providers\LogSmsProvider
     */
    protected function createLogDriver(array $config)
    {
        return new Providers\LogSmsProvider($config);
    }
}
