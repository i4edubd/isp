<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateSpeedController;
use App\Models\Freeradius\customer;
use App\Models\master_package;
use Illuminate\Http\Request;

class MasterPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $master_packages = master_package::with(['pppoe_profile', 'pppoe_profile.ipv4pool',  'pppoe_profile.ipv6pool', 'packages', 'operators'])
            ->where('mgid', $request->user()->id)
            ->get();

        return view('admins.group_admin.master-packages', [
            'master_packages' => $master_packages,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\master_package  $master_package
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, master_package $master_package)
    {
        if (!$request->user()) {
            return 'unauthorized!';
        }

        if ($request->user()->mgid !== $master_package->mgid) {
            return 'unauthorized';
        }

        return view('admins.components.master_package', [
            'master_package' => $master_package,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\master_package  $master_package
     * @return \Illuminate\Http\Response
     */
    public function edit(master_package $master_package)
    {
        $this->authorize('update', $master_package);

        switch ($master_package->connection_type) {
            case 'StaticIp':
                return view('admins.group_admin.static-master-package-edit', [
                    'master_package' => $master_package,
                ]);
                break;

            case 'Other':
                return view('admins.group_admin.other-master-package-edit', [
                    'master_package' => $master_package,
                ]);
                break;

            case 'PPPoE':
            case 'Hotspot':
                return view('admins.group_admin.master-packages-edit', [
                    'master_package' => $master_package,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\master_package  $master_package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, master_package $master_package)
    {
        $this->authorize('update', $master_package);

        if ($request->user()->can('updateName', $master_package)) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);
        }

        switch ($master_package->connection_type) {
            case 'StaticIp':
                $request->validate([
                    'price' => ['required', 'integer'],
                ]);
                break;
            case 'Other':
                $request->validate([
                    'price' => ['required', 'integer'],
                    'operator_price' => ['required', 'integer'],
                ]);
                break;
            case 'PPPoE':
            case 'Hotspot':
                $request->validate([
                    'rate_limit' => ['required', 'integer'],
                    'speed_controller' => ['required', 'in:Router,Radius_Server'],
                    'rate_unit' => ['nullable', 'in:M,k'],
                    'validity' => ['nullable', 'integer'],
                    'volume_limit' => ['required', 'integer'],
                    'volume_unit' => ['required'],
                ]);
                break;
        }

        if ($request->user()->can('updateName', $master_package)) {
            $master_package->name = $request->name;
        }

        if ($request->filled('rate_limit')) {
            $master_package->rate_limit = $request->rate_limit;
        }

        if ($request->filled('rate_unit')) {
            $master_package->rate_unit = $request->rate_unit;
        }

        if ($request->filled('speed_controller')) {
            $master_package->speed_controller = $request->speed_controller;
        }

        if ($request->filled('volume_limit')) {
            $master_package->volume_limit = $request->volume_limit;
        }

        if ($request->filled('volume_unit')) {
            $master_package->volume_unit = $request->volume_unit;
        }

        if ($request->filled('validity')) {
            $master_package->validity = $request->validity;
        } else {
            $master_package->validity = 30;
        }

        if ($request->filled('validity_unit')) {
            $master_package->validity_unit = $request->validity_unit;
        }

        if ($request->filled('price')) {
            $master_package->price = $request->price;
        }

        if ($request->filled('operator_price')) {
            $master_package->operator_price = $request->operator_price;
        }

        if ($request->filled('visibility')) {
            $master_package->visibility = $request->visibility;
        }

        // special
        if ($master_package->name == 'Trial') {
            $master_package->validity_unit = 'Minute';
        }

        $master_package->save();

        if ($master_package->wasChanged('speed_controller') || $master_package->wasChanged('rate_limit') || $master_package->wasChanged('rate_unit') || $master_package->wasChanged('volume_limit') || $master_package->wasChanged('volume_unit')) {
            UpdateSpeedController::dispatch($master_package)
                ->onConnection('database')
                ->onQueue('default');
        }

        return redirect()->route('master_packages.index')->with('success', 'Package has been edited successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\master_package  $master_package
     * @return \Illuminate\Http\Response
     */
    public function destroy(master_package $master_package)
    {
        $this->authorize('delete', $master_package);

        $customer_count = self::customerCount($master_package);

        if ($customer_count == 0) {
            $message = "Master Package: " . $master_package->name . " has been removed successfully!";
            $master_package->delete();
            return redirect()->route('master_packages.index')->with('success', $message);
        } else {
            $message = "Can not delete the package. " . $customer_count . " Customers are using this package.";
            return redirect()->route('master_packages.index')->with('error', $message);
        }
    }

    /**
     * Count Customers
     *
     * @param  \App\Models\master_package  $master_package
     * @return int
     */
    public static function customerCount(master_package $master_package)
    {
        $customer_count = 0;
        foreach ($master_package->packages as $package) {
            $customers = customer::where('package_id', $package->id)->count();
            $customer_count = $customer_count + $customers;
        }
        return $customer_count;
    }
}
