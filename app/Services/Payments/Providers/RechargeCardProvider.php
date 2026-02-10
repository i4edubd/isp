<?php

namespace App\Services\Payments\Providers;

use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Http\Request;
use App\Models\RechargeCard; // Assuming you have this model

class RechargeCardProvider implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * For a recharge card, initiation is handled differently.
     * This method might not be used, or it could redirect to a page
     * where the user enters their card number.
     */
    public function initiatePayment(float $amount, string $currency, string $transactionId, array $meta = [])
    {
        // For this provider, we don't redirect. The user would enter a code on our site.
        // We can simulate a "successful" payment immediately if the code is valid.
        
        $cardCode = $meta['recharge_code'] ?? null;
        if (!$cardCode) {
            throw new \Exception("Recharge card code is required.");
        }

        // 1. Find the card
        $card = RechargeCard::where('code', $cardCode)->where('is_used', false)->first();

        if (!$card || $card->amount < $amount) {
            // event(new PaymentFailed($transactionId, ['reason' => 'Invalid or used card']));
            return false; // Indicate failure
        }
        
        // 2. Mark card as used
        $card->is_used = true;
        $card->used_by_customer_id = $meta['customer_id'];
        $card->save();

        // 3. Dispatch success event
        // event(new PaymentSuccessful($transactionId, $card->toArray()));
        
        return true; // Indicate success
    }

    /**
     * Webhooks are not applicable for this provider.
     */
    public function handleWebhook(Request $request)
    {
        return response()->json(['message' => 'Not applicable.']);
    }

    /**
     * Verification is done instantly during initiation.
     */
    public function verifyPayment(string $transactionId): bool
    {
        // In this model, payment is verified at the time of code submission.
        // You might check your `customer_payments` table here.
        return true;
    }
}
