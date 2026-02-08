<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

class MaestroGateway implements SmsGatewayInterface
{
    /**
     * Send an SMS message using the Maestro gateway (dummy implementation).
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        Log::info("Sending SMS via MaestroGateway to: {$to}. Message: {$message}");
        
        // In a real implementation, you would have API calls here.
        // For this demo, we'll just assume it's always successful.

        // Here we would also log to the sms_histories table.
        // We'll add that logic in the SmsService to keep gateways clean.

        return true;
    }
}
