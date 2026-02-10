<?php

namespace App\Services\Payment;

class DummyGateway implements PaymentGatewayInterface
{
    public function createPayment(array $payload): array
    {
        return ['status' => 'ok', 'redirect' => null, 'transaction_id' => uniqid('tx_', true)];
    }

    public function verifyCallback(array $data): bool
    {
        return isset($data['transaction_id']);
    }
}
