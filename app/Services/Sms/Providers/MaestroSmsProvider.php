<?php

namespace App\Services\Sms\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MaestroSmsProvider implements SmsProviderInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Send an SMS message using the Maestro provider.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        $response = Http::post($this->config['url'], [
            'api_key' => $this->config['api_key'],
            'sender_id' => $this->config['sender_id'],
            'to' => $to,
            'message' => $message,
        ]);

        if ($response->successful()) {
            Log::info("SMS sent to {$to} via Maestro.");
            return true;
        }

        Log::error("Failed to send SMS to {$to} via Maestro. Response: " . $response->body());
        return false;
    }
}
