<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomerPayment\CustomersPaymentProcessController;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Carbon\Carbon;

class AutoDebitController extends Controller
{
    /**
     * Process advance payment.
     *
     * @return int
     */
    public static function store()
    {
        // group admins
        $group_admins = operator::where('role', 'group_admin')->get();

        while ($group_admin = $group_admins->shift()) {

            $model = new customer();
            $model->setConnection($group_admin->radius_db_connection);

            $customer_where = [
                ['mgid', '=', $group_admin->mgid],
                ['payment_status', '=', 'billed'],
                ['advance_payment', '>', 1],
            ];

            $customers = $model->where($customer_where)->get();

            while ($customer = $customers->shift()) {

                $bill_where = [
                    ['operator_id', '=', $customer->operator_id],
                    ['customer_id', '=', $customer->id],
                ];

                $bills = customer_bill::where($bill_where)->get();

                foreach ($bills as $bill) {

                    if ($customer->advance_payment < 1) {
                        continue;
                    }

                    // Partial Payment
                    if ($customer->advance_payment < $bill->amount) {

                        $new_bill = new customer_bill();
                        $new_bill->mgid = $bill->mgid;
                        $new_bill->gid = $bill->gid;
                        $new_bill->operator_id = $bill->operator_id;
                        $new_bill->parent_customer_id = $bill->parent_customer_id;
                        $new_bill->customer_id = $bill->customer_id;
                        $new_bill->package_id = $bill->package_id;
                        $new_bill->customer_zone_id = $bill->customer_zone_id;
                        $new_bill->name = $bill->name;
                        $new_bill->mobile = $bill->mobile;
                        $new_bill->username = $bill->username;
                        $new_bill->amount = round($bill->amount - $customer->advance_payment);
                        $new_bill->currency = $bill->currency;
                        $new_bill->description = $bill->description;
                        $new_bill->billing_period = $bill->billing_period;
                        $new_bill->due_date = $bill->due_date;
                        $new_bill->purpose = PaymentPurpose::PAYMENT_AFTER_ADVANCE->value;
                        $new_bill->remark = config('consumer.currency') . ' ' . $customer->advance_payment . ' adjusted from advance payment on ' . date(config('app.date_format'));
                        $new_bill->year = $bill->year;
                        $new_bill->month = $bill->month;
                        $new_bill->save();
                        $new_bill->operator_amount = CustomerBillController::operatorAmount($new_bill);
                        $new_bill->validity_period = BillingHelper::getBillValidity($new_bill);
                        $new_bill->save();

                        $customer_payment = new customer_payment();
                        $customer_payment->mgid = $bill->mgid;
                        $customer_payment->gid = $bill->gid;
                        $customer_payment->operator_id = $bill->operator_id;
                        $customer_payment->customer_id = $bill->customer_id;
                        $customer_payment->customer_bill_id = $bill->id;
                        $customer_payment->package_id = $bill->package_id;
                        $customer_payment->payment_gateway_name = 'Cash';
                        $customer_payment->mobile = $bill->mobile;
                        $customer_payment->name = $bill->name;
                        $customer_payment->username = $bill->username;
                        $customer_payment->type = 'Cash';
                        $customer_payment->payment_mode = 'prepaid';
                        $customer_payment->pay_status = 'Successful';
                        $customer_payment->amount_paid = $customer->advance_payment;
                        $customer_payment->store_amount = $customer->advance_payment;
                        $customer_payment->discount = 0;
                        $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
                        $customer_payment->date = date(config('app.date_format'));
                        $customer_payment->week = date(config('app.week_format'));
                        $customer_payment->month = date(config('app.month_format'));
                        $customer_payment->year = date(config('app.year_format'));
                        $customer_payment->used = 0;
                        $customer_payment->require_sms_notice = 0;
                        $customer_payment->require_accounting = 1;
                        $customer_payment->purpose = PaymentPurpose::PAYMENT_FROM_ADVANCE->value;
                        $customer_payment->save();
                        CustomersPaymentProcessController::store($customer_payment);

                        // update customer
                        $customer->advance_payment = 0;
                        $customer->save();
                    }


                    if ($customer->advance_payment == $bill->amount) {
                        $customer_payment = new customer_payment();
                        $customer_payment->mgid = $bill->mgid;
                        $customer_payment->gid = $bill->gid;
                        $customer_payment->operator_id = $bill->operator_id;
                        $customer_payment->customer_id = $bill->customer_id;
                        $customer_payment->customer_bill_id = $bill->id;
                        $customer_payment->package_id = $bill->package_id;
                        $customer_payment->validity_period = $bill->validity_period;
                        $customer_payment->payment_gateway_name = 'Cash';
                        $customer_payment->mobile = $bill->mobile;
                        $customer_payment->name = $bill->name;
                        $customer_payment->username = $bill->username;
                        $customer_payment->type = 'Cash';
                        $customer_payment->payment_mode = 'prepaid';
                        $customer_payment->pay_status = 'Successful';
                        $customer_payment->amount_paid = $customer->advance_payment;
                        $customer_payment->store_amount = $customer->advance_payment;
                        $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
                        $customer_payment->date = date(config('app.date_format'));
                        $customer_payment->week = date(config('app.week_format'));
                        $customer_payment->month = date(config('app.month_format'));
                        $customer_payment->year = date(config('app.year_format'));
                        $customer_payment->used = 0;
                        $customer_payment->require_sms_notice = 0;
                        $customer_payment->require_accounting = 1;
                        $customer_payment->purpose = $bill->purpose;
                        $customer_payment->save();
                        CustomersPaymentProcessController::store($customer_payment);

                        // update customer
                        $customer->advance_payment = 0;
                        $customer->save();
                    }

                    if ($customer->advance_payment > $bill->amount) {
                        $customer_payment = new customer_payment();
                        $customer_payment->mgid = $bill->mgid;
                        $customer_payment->gid = $bill->gid;
                        $customer_payment->operator_id = $bill->operator_id;
                        $customer_payment->customer_id = $bill->customer_id;
                        $customer_payment->customer_bill_id = $bill->id;
                        $customer_payment->package_id = $bill->package_id;
                        $customer_payment->validity_period = $bill->validity_period;
                        $customer_payment->payment_gateway_name = 'Cash';
                        $customer_payment->mobile = $bill->mobile;
                        $customer_payment->name = $bill->name;
                        $customer_payment->username = $bill->username;
                        $customer_payment->type = 'Cash';
                        $customer_payment->payment_mode = 'prepaid';
                        $customer_payment->pay_status = 'Successful';
                        $customer_payment->amount_paid = $bill->amount;
                        $customer_payment->store_amount = $bill->amount;
                        $customer_payment->mer_txnid = Carbon::now(config('app.timezone'))->timestamp;
                        $customer_payment->date = date(config('app.date_format'));
                        $customer_payment->week = date(config('app.week_format'));
                        $customer_payment->month = date(config('app.month_format'));
                        $customer_payment->year = date(config('app.year_format'));
                        $customer_payment->used = 0;
                        $customer_payment->require_sms_notice = 0;
                        $customer_payment->require_accounting = 1;
                        $customer_payment->purpose = $bill->purpose;
                        $customer_payment->save();
                        CustomersPaymentProcessController::store($customer_payment);

                        // update customer
                        $customer->advance_payment = $customer->advance_payment - $bill->amount;
                        $customer->save();
                    }
                }
            }
        }

        return 0;
    }
}
