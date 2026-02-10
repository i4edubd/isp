<?php

namespace App\Http\Controllers;

use App\Models\operator;

class SubscriptionStatusController extends Controller
{
    /**
     * Activate Group Admin after payment
     *
     * @return int
     */
    public static function activate(operator $group_admin)
    {

        $operators = operator::where('mgid', $group_admin->mgid)->get();

        while ($operator = $operators->shift()) {
            $operator->subscription_status = 'active';
            $operator->save();
            CacheController::forgetOperator($operator->id);
        }

        return 1;
    }

    /**
     * Suspend Group Admins due to bill issue
     *
     * @return int
     */
    public static function suspend(operator $group_admin)
    {
        $operators = operator::where('mgid', $group_admin->mgid)->get();

        while ($operator = $operators->shift()) {
            $operator->subscription_status = 'suspended';
            $operator->save();
            CacheController::forgetOperator($operator->id);
        }

        return 1;
    }
}
