<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\pgsql\pgsql_customer;

class PgsqlCustomerController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {
        $operator = CacheController::getOperator($customer->operator_id);
        $model = new pgsql_customer();
        $model->setConnection($operator->pgsql_connection);
        $model->updateOrCreate(
            ['mgid' => $customer->mgid, 'customer_id' => $customer->id],
            [
                'operator_id' => $customer->operator_id,
                'username' => $customer->username,
                'login_mac_address' => $customer->login_mac_address,
                'mac_bind' => $customer->mac_bind,
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public static function destroy(customer $customer)
    {
        $operator = CacheController::getOperator($customer->operator_id);
        $model = new pgsql_customer();
        $model->setConnection($operator->pgsql_connection);
        $model->where('username', $customer->username)->delete();
    }
}
