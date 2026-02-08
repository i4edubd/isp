<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Mail\GreetingFromIspBillingSolution;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GreetSelfRegisteredOperatorsController extends Controller
{

    /**
     * Send Greeting email to self registerd operator.
     *
     * @return int
     */

    public static function greetOperator($operator_id)
    {
        $operator = operator::findOrFail($operator_id);

        if ($operator->role !== 'group_admin') {
            return 0;
        }

        if ($operator->provisioning_status == 2) {
            return 0;
        }

        if (!strlen($operator->email_verified_at)) {
            return 0;
        }

        if ($operator->sp_request == 1) {
            return 0;
        }

        if ($operator->sd_request == 1) {
            return 0;
        }

        Mail::mailer('sales')
            ->to($operator)
            ->send(new GreetingFromIspBillingSolution($operator));

        return 0;
    }


    /**
     *  Send Greeting email to all self registerd operator.
     *
     * @return int
     */
    public static function greetAllOperator()
    {

        if (config('consumer.broadcast_sales_email') == false) {
            return 0;
        }

        $where = [
            ['role', '=', 'group_admin'],
            ['provisioning_status', '!=', 2],
        ];

        $operators = operator::where($where)->get();

        foreach ($operators as $operator) {

            if ($operator->sp_request == 1) {
                $operator->provisioning_status = 2;
                $operator->save();
                continue;
            }

            if ($operator->sd_request == 1) {
                OperatorDeleteController::deleteGroupAdmin($operator);
                continue;
            }

            if (!strlen($operator->email_verified_at)) {
                OperatorDeleteController::deleteGroupAdmin($operator);
                continue;
            }

            if ($operator->mrk_email_count >= 3) {
                $model = new customer();
                $model->setConnection($operator->radius_db_connection);
                $count = $model->where('mgid', $operator->id)->get()->count();
                if ($count == 0) {
                    OperatorDeleteController::deleteGroupAdmin($operator);
                }
                continue;
            }

            self::greetOperator($operator->id);

            $message = "Dear Sir, Please check your email $operator->email and respond to the email from " . config('mail.mailers.sales.from.address') . " - ISPbills";

            if (validate_mobile($operator->mobile)) {
                $controller = new SmsGatewayController();
                $controller->sendSms($operator, $operator->mobile, $message);
            }
        }

        return 0;
    }
}
