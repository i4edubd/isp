<?php

namespace App\Services\Payments\Providers;

use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class SslCommerzProvider implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function initiatePayment(float $amount, string $currency, string $transactionId, array $meta = [])
    {
        $post_data = [];
        $post_data['store_id'] = $this->config['store_id'];
        $post_data['store_passwd'] = $this->config['store_password'];
        $post_data['total_amount'] = $amount;
        $post_data['currency'] = $currency;
        $post_data['tran_id'] = $transactionId;
        $post_data['success_url'] = route('payment.webhook', ['gateway' => 'sslcommerz']);
        $post_data['fail_url'] = route('payment.webhook', ['gateway' => 'sslcommerz']);
        $post_data['cancel_url'] = route('payment.webhook', ['gateway' => 'sslcommerz']);
        
        // Customer info
        $post_data['cus_name'] = $meta['customer_name'] ?? 'N/A';
        $post_data['cus_email'] = $meta['customer_email'] ?? 'email@example.com';
        
        // In a real application, you would make an HTTP request to SSLCommerz
        // and then redirect to their payment page. This is a simplified example.
        
        // For demonstration, we will just redirect to a placeholder page.
        // In a real implementation, you would get a redirect URL from the API response.
        $this->log("Initiating payment for transaction {$transactionId}. Redirecting to placeholder.");

        // Placeholder redirect
        return Redirect::to('https://sandbox.sslcommerz.com/gwprocess/v4/dummy.php');
    }

    public function handleWebhook(Request $request)
    {
        // In a real application, you would validate the hash and process the payment status.
        $this->log('Received webhook notification.', $request->all());
        
        $transactionId = $request->input('tran_id');
        $status = $request->input('status'); // e.g., 'VALID' or 'FAILED'

        if ($status === 'VALID') {
            // Here you would dispatch an event like PaymentSuccessful
            // event(new PaymentSuccessful($transactionId, $request->all()));
            $this->log("Payment for {$transactionId} was successful.");
        } else {
            // event(new PaymentFailed($transactionId, $request->all()));
            $this->log("Payment for {$transactionId} failed or was cancelled.");
        }

        return response()->json(['status' => 'success']);
    }

    public function verifyPayment(string $transactionId): bool
    {
        // Real implementation would call SSLCommerz's verification endpoint.
        $this->log("Verifying payment for transaction {$transactionId}.");
        return true; // Assume success for this example
    }
    
    protected function log($message, $context = [])
    {
        // A helper for logging
        \Illuminate\Support\Facades\Log::info('[SSLCommerz] ' . $message, $context);
    }
}
