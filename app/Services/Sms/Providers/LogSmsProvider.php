<?php

namespace App\Services\Sms\Providers;

use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProviderInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Send an SMS message by writing it to the log.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        Log::channel($this->config['channel'] ?? 'null')->info("Sending SMS to {$to}: {$message}");
        return true;
    }
}
