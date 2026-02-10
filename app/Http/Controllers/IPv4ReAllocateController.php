<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Models\Freeradius\customer;
use App\Models\ipv4pool;
use App\Models\operator;
use Illuminate\Http\Request;

class IPv4ReAllocateController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\ipv4pool $ipv4pool
     * @return \Illuminate\Http\Response
     */
    public static function store(ipv4pool $ipv4pool)
    {
        // suspended_users_pool
        if ($ipv4pool->name == 'suspended_users_pool') {
            return self::reAllocateSuspendedPool($ipv4pool);
        }

        // regular IPv4 Pools
        $madmin = operator::find($ipv4pool->mgid);

        $pppoe_profiles = $ipv4pool->pppoe_profiles;

        foreach ($pppoe_profiles as $pppoe_profile) {

            $mpackages = $pppoe_profile->master_packages;

            foreach ($mpackages as $mpackage) {

                $packages = $mpackage->packages;

                foreach ($packages as $package) {
                    $model = new customer();
                    $model->setConnection($madmin->node_connection);
                    $customers = $model->where('package_id', $package->id)->get();
                    foreach ($customers as $customer) {
                        PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                    }
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\ipv4pool $ipv4pool
     * @return \Illuminate\Http\Response
     */
    public static function reAllocateSuspendedPool(ipv4pool $ipv4pool)
    {
        if ($ipv4pool->name !== 'suspended_users_pool') {
            return false;
        }

        $madmin = operator::find($ipv4pool->mgid);
        $model = new customer();
        $model->setConnection($madmin->node_connection);
        $customers = $model->where('mgid', $madmin->id)
            ->where('connection_type', 'PPPoE')
            ->where('status', 'suspended')
            ->get();

        foreach ($customers as $customer) {
            PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
            PPPCustomerDisconnectController::disconnect($customer);
        }
    }
}
