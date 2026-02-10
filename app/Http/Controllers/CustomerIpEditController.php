<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Models\Freeradius\customer;
use App\Models\ipv4address;
use Illuminate\Http\Request;
use Net_IPv4;

class CustomerIpEditController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(customer $customer)
    {
        $this->authorize('editIP', [$customer]);
        $package = CacheController::getPackage($customer->package_id);
        $pppoe_profile = $package->master_package->pppoe_profile;
        if ($pppoe_profile->ip_allocation_mode == 'dynamic') {
            return 'IP Cannot be modified when ppp profile IP allocation mode is dynamic.';
        }

        $pool = $pppoe_profile->ipv4pool;
        $ippool = long2ip($pool->subnet) . '/' . $pool->mask;

        return view('admins.components.ip-edit-form', [
            'customer' => $customer,
            'package' => $package,
            'ippool' => $ippool,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer $customer)
    {

        $request->validate([
            'login_ip' => 'required|string',
        ]);

        if ($customer->login_ip == $request->login_ip) {
            return redirect(url()->previous());
        }

        $ipv4lib = new Net_IPv4();

        if ($ipv4lib->validateIP($request->login_ip) == false) {
            return redirect(url()->previous())->with('info', 'Invalid IP Address');
        }

        $login_ip = $request->login_ip;

        $package = CacheController::getPackage($customer->package_id);
        $pppoe_profile = $package->master_package->pppoe_profile;
        $pool = $pppoe_profile->ipv4pool;

        if (ipv4address::where('customer_id', '!=', $customer->id)
            ->where('operator_id', $customer->operator_id)
            ->where('ipv4pool_id', $pool->id)
            ->where('ip_address', $ipv4lib->ip2double($login_ip))
            ->count()
        ) {
            return redirect(url()->previous())->with('info', 'Duplicate IP Address');
        }

        ipv4address::updateOrCreate(
            ['customer_id' => $customer->id, 'operator_id' => $customer->operator_id, 'ipv4pool_id' => $pool->id],
            ['ip_address' => $ipv4lib->ip2double($login_ip)]
        );

        $customer->login_ip = $login_ip;
        $customer->save();

        PPPoECustomersFramedIPAddressController::updateOrCreate($customer);

        PPPCustomerDisconnectController::disconnect($customer);

        return redirect(url()->previous())->with('info', 'IP Address Updated');
    }
}
