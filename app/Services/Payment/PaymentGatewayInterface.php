<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    /**
     * Create a payment request and return a redirect or token payload.
     *
     * @param array $payload
     * @return array
     */
    public function createPayment(array $payload): array;

    /**
     * Verify webhook or callback payload.
     *
     * @param array $data
     * @return bool
     */
    public function verifyCallback(array $data): bool;
}
