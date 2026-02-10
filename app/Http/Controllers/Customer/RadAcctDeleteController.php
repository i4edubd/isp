<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\operator;
use App\Models\pgsql\pgsql_radacct_history;
use Illuminate\Http\Request;

class RadAcctDeleteController extends Controller
{
    public static function deleteRadaccts(customer $customer)
    {
        $group_admin = operator::find($customer->mgid);

        $model = new radacct();
        $model->setConnection($group_admin->radius_db_connection);
        $model->where('username', $customer->username)
            ->whereNotNull('acctstoptime')
            ->delete();

        $model = new pgsql_radacct_history();
        $model->setConnection($group_admin->pgsql_connection);
        $model->where('username', $customer->username)->delete();
    }
}
