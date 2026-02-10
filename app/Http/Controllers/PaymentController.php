<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Redirect the user to the payment gateway.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'gateway' => 'sometimes|string', // Optional: specify gateway
        ]);

        $amount = $request->input('amount');
        $gateway = $request->input('gateway', config('payment-gateways.default'));
        $transactionId = 'TXN-' . strtoupper(Str::random(12));
        
        // In a real application, you would create a record in your `customer_payments`
        // table here with a 'pending' status and the transaction ID.
        
        $meta = [
            'customer_name' => auth()->user()->name,
            'customer_email' => auth()->user()->email,
            'customer_id' => auth()->id(),
        ];
        
        return $this->paymentService->driver($gateway)->initiatePayment($amount, 'BDT', $transactionId, $meta);
    }

    /**
     * Handle webhook notifications from the payment gateway.
     *
     * @param Request $request
     * @param string $gateway
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request, string $gateway)
    {
        return $this->paymentService->driver($gateway)->handleWebhook($request);
    }
}