<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment and redirect the user to the payment gateway.
     *
     * @param float $amount
     * @param string $currency
     * @param string $transactionId // Your internal unique transaction ID
     * @param array $meta // Additional data (customer info, etc.)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initiatePayment(float $amount, string $currency, string $transactionId, array $meta = []);

    /**
     * Handle the webhook notification from the payment gateway.
     *
     * @param Request $request
     * @return mixed // Typically a response to the gateway and an event dispatch
     */
    public function handleWebhook(Request $request);

    /**
     * Verify the payment status.
     *
     * @param string $transactionId
     * @return bool // Returns true if the payment was successful
     */
    public function verifyPayment(string $transactionId): bool;
}
