<?php

namespace App\Services;

use App\Services\Sms\SmsGatewayInterface;
use App\Models\SmsHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SmsService
{
    protected $gateway;

    public function __construct(SmsGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Send an SMS and log the history.
     *
     * @param string $to
     * @param string $message
     * @param int|null $customerId
     * @return bool
     */
    public function send(string $to, string $message, int $customerId = null): bool
    {
        $success = $this->gateway->send($to, $message);

        // Log the transaction
        $this->logSms($to, $message, $success, $customerId);

        return $success;
    }

    /**
     * Log the SMS to the database.
     */
    protected function logSms(string $to, string $message, bool $success, ?int $customerId): void
    {
        $now = Carbon::now();

        SmsHistory::create([
            'to_number' => $to,
            'sms_body' => $message,
            'status_text' => $success ? 'Successful' : 'Failed',
            'operator_id' => Auth::id(), // Assuming the logged-in user is an operator
            'customer_id' => $customerId,
            // 'sms_gateway_id' => $this->gateway->getId(), // Would be useful to add
            // 'sms_cost' => $this->gateway->getCost(), // Would be useful
            'date' => $now->toDateString(),
            'week' => $now->weekOfYear,
            'month' => $now->month,
            'year' => $now->year,
        ]);
    }
}
