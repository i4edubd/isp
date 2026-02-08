<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\BdsmartpayController;
use App\Http\Controllers\Payment\BkashCheckoutController;
use App\Http\Controllers\Payment\bKashTokenizedCustomerPaymentController;
use App\Http\Controllers\Payment\EasypaywayTransactionController;
use App\Http\Controllers\Payment\NagadPaymentGatewayController;
use App\Http\Controllers\Payment\ShurjoPayCustomerPaymentController;
use App\Http\Controllers\Payment\SslcommerzTransactionController;
use App\Http\Controllers\Payment\WalletMixGatewayController;
use App\Models\customer_payment;
use App\Models\payment_gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecheckCustomerPaymentScheduler extends Controller
{

    /**
     * Recheck Customers Pending Payments
     *
     * @return int
     */
    public static function recheck()
    {
        $where = [
            ['pay_status', '!=', 'Successful'],
        ];

        $pending_payments = customer_payment::where($where)->get();

        foreach ($pending_payments as $customer_payment) {

            $timeout = Carbon::now(getTimeZone($customer_payment->operator_id))->subMinutes(15);

            $payment_time = Carbon::createFromFormat('Y-m-d H:i:s', $customer_payment->created_at, getTimeZone($customer_payment->operator_id));

            if ($timeout->lessThan($payment_time)) {
                continue;
            }

            if ($customer_payment->payment_gateway_id == 0) {
                $customer_payment->delete();
            }

            $payment_gateway = payment_gateway::where('id', '=', $customer_payment->payment_gateway_id)->firstOr(function () {
                return payment_gateway::make([
                    'id' => 0,
                ]);
            });

            if ($payment_gateway->id == 0) {
                $customer_payment->delete();
            }

            switch ($payment_gateway->provider_name) {
                case 'easypayway':
                    $controller = new EasypaywayTransactionController();
                    $controller->recheckCustomerPayment($customer_payment);
                    break;
                case 'sslcommerz':
                    $controller = new SslcommerzTransactionController();
                    $controller->recheckCustomerPayment($customer_payment);
                    break;
                case 'bkash_checkout':
                    $controller = new BkashCheckoutController();
                    $controller->recheckCustomerPayment($customer_payment);
                    break;
                case 'nagad':
                    $controller = new NagadPaymentGatewayController();
                    $controller->recheckCustomerPayment($customer_payment);
                    break;
                case 'walletmix':
                    $controller = new WalletMixGatewayController();
                    $controller->recheckCustomerPayment($customer_payment);
                    break;
                case 'bdsmartpay':
                    $controller = new BdsmartpayController();
                    $controller->recheckCustomerPayment($customer_payment);
                    break;
                case 'bkash_tokenized_checkout':
                    $controller = new bKashTokenizedCustomerPaymentController();
                    $controller->recheckPayment($customer_payment);
                    break;
                case 'shurjopay':
                    $controller = new ShurjoPayCustomerPaymentController();
                    $controller->verifyPayment($customer_payment);
                    break;
            }
        }
        return 0;
    }
}
