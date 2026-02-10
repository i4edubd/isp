<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

class CompositeGateway implements SmsGatewayInterface
{
    /** @var SmsGatewayInterface[] */
    protected array $gateways = [];

    public function __construct(array $gateways)
    {
        $this->gateways = $gateways;
    }

    public function send(string $to, string $message): bool
    {
        $lastException = null;

        foreach ($this->gateways as $gw) {
            try {
                $ok = $gw->send($to, $message);
                if ($ok) {
                    return true;
                }
            } catch (\Throwable $e) {
                $lastException = $e;
                Log::warning('SMS gateway failed, trying next', ['error' => $e->getMessage()]);
            }
        }

        if ($lastException) {
            Log::error('All SMS gateways failed', ['error' => $lastException->getMessage()]);
        }

        return false;
    }
}
