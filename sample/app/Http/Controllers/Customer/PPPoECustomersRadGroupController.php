<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\pgsql\pgsql_radusergroup;
use Illuminate\Http\Request;

class PPPoECustomersRadGroupController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public static function updateOrCreate(customer $customer)
    {
        // <<shutdown : 05/05/2021
        return 0;
        // shutdown>>

        $group_admin = operator::find($customer->mgid);

        $radusergroup = new pgsql_radusergroup();

        $radusergroup->setConnection($group_admin->pgsql_connection);

        $radusergroup->updateOrCreate(
            [
                'mgid' => $customer->mgid,
                'username' => $customer->username,
            ],
            [
                'groupname' => 'dialup',
            ]
        );
    }
}
