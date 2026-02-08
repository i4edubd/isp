<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomersRadLimitController;
use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Models\fair_usage_policy;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\master_package;
use App\Models\operator;
use App\Models\pgsql\pgsql_radacct_history;
use Illuminate\Http\Request;

class ActivateFupController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(customer $customer)
    {
        $this->authorize('activateFup', $customer);

        $customer->status = 'fup';

        $customer->save();

        PPPoECustomersFramedIPAddressController::updateOrCreate($customer);

        CustomersRadLimitController::updateOrCreate($customer);

        PPPCustomerDisconnectController::disconnect($customer);

        return 'Fair Usage Policy has been activated successfully';
    }

    /**
     * Update the specified resource in storage.
     *
     * @return int
     */
    public static function autoFup()
    {

        // get ppp packages
        $master_packages = master_package::where('connection_type', 'PPPoE')->get();

        // check for each package
        foreach ($master_packages as $master_package) {

            // if the package has fair usage policy
            if (fair_usage_policy::where('master_package_id', $master_package->id)->count()) {

                $fair_usage_policy = fair_usage_policy::where('master_package_id', $master_package->id)->first();

                $octet_limit = $fair_usage_policy->total_octet_limit;

                $group_admin = operator::find($master_package->mgid);

                $packages = $master_package->packages;

                foreach ($packages as $package) {

                    $model = new customer();

                    $model->setConnection($group_admin->radius_db_connection);

                    $customers_where = [
                        ['status', '=', 'active'],
                        ['package_id', '=', $package->id],
                    ];

                    // get all active customers using this package
                    $customers = $model->where($customers_where)->get();

                    foreach ($customers as $customer) {
                        //usage
                        $radacct = new radacct();
                        $radacct->setConnection($group_admin->radius_db_connection);
                        $download = $radacct->where('username', '=', $customer->username)->sum('acctoutputoctets');
                        $upload = $radacct->where('username', '=', $customer->username)->sum('acctinputoctets');

                        // usage 2
                        $radacct_history = new pgsql_radacct_history();
                        $radacct_history->setConnection($group_admin->pgsql_connection);
                        $download_histroy = $radacct_history->where('username', '=', $customer->username)->sum('acctoutputoctets');
                        $upload_histroy = $radacct_history->where('username', '=', $customer->username)->sum('acctinputoctets');

                        $usage =  $download + $upload + $download_histroy + $upload_histroy;

                        if ($usage > $octet_limit) {

                            $customer->status = 'fup';

                            $customer->save();

                            PPPoECustomersFramedIPAddressController::updateOrCreate($customer);

                            CustomersRadLimitController::updateOrCreate($customer);

                            PPPCustomerDisconnectController::disconnect($customer);
                        }
                    }
                }
            }
        }

        return 0;
    }
}
