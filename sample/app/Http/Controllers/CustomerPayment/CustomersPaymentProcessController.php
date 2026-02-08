<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\AfterPaymentCustomerServiceController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\HotspotInternetLoginController;
use App\Http\Controllers\ManagersCashCollectionAccountingController;
use App\Http\Controllers\PaymentGatewayServiceChargeController;
use App\Http\Controllers\PPPCustomerDisconnectController;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Jobs\CustomersPaymentProcessJob;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Support\Facades\Log;

class CustomersPaymentProcessController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public static function store(customer_payment $customer_payment)
    {
        CustomersPaymentProcessJob::dispatch($customer_payment)
            ->onConnection('database')
            ->onQueue('customer_payments');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public static function doStore(customer_payment $customer_payment)
    {
        $customer_payment->refresh();

        //Successful
        if ($customer_payment->pay_status !== 'Successful') {
            return 0;
        }

        //not used
        if ($customer_payment->used !== 0) {
            return 0;
        }

        // Process Payment Gateway Service Charge
        $customer_payment = PaymentGatewayServiceChargeController::customerPayment($customer_payment);

        //delete bills
        if ($customer_payment->customer_bill_id > 0) {
            customer_bill::where('id', $customer_payment->customer_bill_id)->delete();
        }

        // operator
        $operator = operator::findOrFail($customer_payment->operator_id);

        // customer
        $model = new customer();
        $model->setConnection($operator->radius_db_connection);
        $customer = $model->findOrFail($customer_payment->customer_id);

        // require disconnect customer
        $disconnect = 1;
        if ($customer->connection_type == 'PPPoE' && $customer->status == 'suspended') {
            $disconnect = 1;
        }

        // payment_status
        $bill_where = [
            ['operator_id', '=', $customer_payment->operator_id],
            ['customer_id', '=', $customer_payment->customer_id],
        ];

        $count_bill = customer_bill::where($bill_where)->count();

        //update customer
        if ($count_bill) {
            $customer->payment_status = 'billed';
        } else {
            $customer->payment_status = 'paid';
        }
        $customer->status = 'active';
        $customer->texted_locked_status = 0;
        $customer->save();

        // After Payment Service
        AfterPaymentCustomerServiceController::update($customer_payment);

        // Accounting
        $controller = new CustomerPaymentAccountingController();
        $controller->distributePaymet($customer_payment);
        ManagersCashCollectionAccountingController::doAccounts($customer_payment);

        // login
        if ($customer->connection_type == 'Hotspot') {
            HotspotInternetLoginController::login($customer);
        }

        // disconnect
        if ($disconnect) {
            PPPCustomerDisconnectController::disconnect($customer);
        }

        // mark the payment as used
        $customer_payment->used = 1;
        $customer_payment->save();

        // zero payment
        if ($customer_payment->amount_paid == 0) {
            $customer_payment->delete();
            return 1;
        }

        // require_sms_notice
        if ($customer_payment->require_sms_notice == 0) {
            return 1;
        }

        // sms
        $mobile = validate_mobile($customer->mobile);
        if ($mobile) {
            try {
                if ($customer_payment->payment_gateway_name === 'recharge_card') {
                    $message = SmsGenerator::cardRechargeSuccessfulMsg($operator, $customer_payment->amount_paid);
                } else {
                    $message = SmsGenerator::paymentConfirmationMsg($operator, $customer_payment->amount_paid);
                }
                $controller = new SmsGatewayController();
                $controller->sendSms($operator, $mobile, $message, $customer->id);
            } catch (\Throwable $th) {
                Log::channel('debug')->debug($th);
            }
        }

        return 1;
    }
}
