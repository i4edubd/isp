<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerActivateController;
use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Models\fair_usage_policy;
use App\Models\Freeradius\customer;
use App\Models\ipv4address;
use App\Models\ipv4pool;
use App\Models\master_package;
use App\Models\package;
use Illuminate\Http\Request;

class FairUsagePolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\master_package  $master_package
     * @return \Illuminate\Http\Response
     */
    public function index(master_package $master_package)
    {
        $fair_usage_policy = fair_usage_policy::where('master_package_id', $master_package->id)->firstOr(function () {
            return
                fair_usage_policy::make([
                    'id' => 0,
                    'mgid' => 0,
                    'master_package_id' => 0,
                    'data_limit' => 0,
                    'speed_limit' => 0,
                    'ipv4pool_id' => 0,
                ]);
        });

        return view('admins.components.package-fair-usage-policy', [
            'master_package' => $master_package,
            'fair_usage_policy' => $fair_usage_policy,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\master_package  $master_package
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, master_package $master_package)
    {
        $ipv4pools = ipv4pool::where('mgid', $request->user()->id)->get();

        return view('admins.group_admin.package-fair-usage-policy-create', [
            'master_package' => $master_package,
            'ipv4pools' => $ipv4pools,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\master_package  $master_package
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, master_package $master_package)
    {
        $request->validate([
            'data_limit' => 'required|numeric',
            'speed_limit' => 'required|numeric',
            'ipv4pool_id' => 'required|numeric',
        ]);

        $fair_usage_policy = new fair_usage_policy();
        $fair_usage_policy->mgid = $request->user()->id;
        $fair_usage_policy->master_package_id = $master_package->id;
        $fair_usage_policy->data_limit = $request->data_limit;
        $fair_usage_policy->speed_limit = $request->speed_limit;
        $fair_usage_policy->ipv4pool_id = $request->ipv4pool_id;
        $fair_usage_policy->save();

        return redirect()->route('master_packages.index')->with('success', 'Fair Usage Policy Saved successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\master_package  $master_package
     * @param  \App\Models\fair_usage_policy  $fair_usage_policy
     * @return \Illuminate\Http\Response
     */
    public function edit(master_package $master_package, fair_usage_policy $fair_usage_policy)
    {
        $pools = ipv4pool::where('mgid', $fair_usage_policy->mgid)->get();

        $ipv4pools = $pools->except($fair_usage_policy->ipv4pool_id);

        return view('admins.group_admin.package-fair-usage-policy-edit', [
            'master_package' => $master_package,
            'fair_usage_policy' => $fair_usage_policy,
            'ipv4pools' => $ipv4pools,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\master_package  $master_package
     * @param  \App\Models\fair_usage_policy  $fair_usage_policy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, master_package $master_package, fair_usage_policy $fair_usage_policy)
    {
        if ($request->user()->id !== $master_package->mgid || $request->user()->id !== $fair_usage_policy->mgid) {
            abort(403);
        }

        $request->validate([
            'data_limit' => 'required|numeric',
            'speed_limit' => 'required|numeric',
            'ipv4pool_id' => 'required|numeric',
        ]);

        $from_ipv4pool = $fair_usage_policy->ipv4pool;

        $to_ipv4pool = ipv4pool::findOrFail($request->ipv4pool_id);

        $fair_usage_policy->data_limit = $request->data_limit;
        $fair_usage_policy->speed_limit = $request->speed_limit;
        $fair_usage_policy->ipv4pool_id = $request->ipv4pool_id;
        $fair_usage_policy->save();

        // Re allocate IPv4 address
        if ($from_ipv4pool->id !== $to_ipv4pool->id) {

            $packages = $master_package->packages;

            foreach ($packages as $package) {
                $where = [
                    ['package_id', '=', $package->id],
                    ['status', '=', 'fup'],
                ];
                $customers = customer::where($where)->get();
                foreach ($customers as $customer) {
                    PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                }
            }
        }

        return redirect()->route('master_packages.index')->with('success', 'Fair Usage Policy Saved successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\master_package  $master_package
     * @param  \App\Models\fair_usage_policy  $fair_usage_policy
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, master_package $master_package, fair_usage_policy $fair_usage_policy)
    {
        if ($request->user()->id !== $master_package->mgid || $request->user()->id !== $fair_usage_policy->mgid) {
            abort(403);
        }

        $packages = $master_package->packages;

        foreach ($packages as $package) {
            $where = [
                ['package_id', '=', $package->id],
                ['status', '=', 'fup'],
            ];
            $customers = customer::where($where)->get();

            $ipv4pool = ipv4pool::findOrFail($fair_usage_policy->ipv4pool_id);

            foreach ($customers as $customer) {
                $where = [
                    ['customer_id', '=', $customer->id],
                    ['ipv4pool_id', '=', $ipv4pool->id],
                ];
                ipv4address::where($where)->delete();
                $controller = new CustomerActivateController();
                $controller->update($customer);
            }
        }

        $fair_usage_policy->delete();

        return redirect()->route('master_packages.index')->with('success', 'Fair Usage Policy Removed successfully!');
    }
}
