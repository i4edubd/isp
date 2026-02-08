<?php

namespace App\Services\Sms;

interface SmsGatewayInterface
{
    /**
     * Send an SMS message.
     *
     * @param string $to
     * @param string $message
     * @return bool
     */
    public function send(string $to, string $message): bool;
}
