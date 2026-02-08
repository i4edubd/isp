<?php

namespace App\Http\Controllers;

use App\Models\custom_price;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\master_package;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $packages = package::with(['master_package', 'child_packages'])->where('operator_id', $operator->id)->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.packages', [
                    'operator' => $operator,
                    'packages' => $packages,
                ]);
                break;

            case 'operator':
                return view('admins.operator.packages', [
                    'operator' => $operator,
                    'packages' => $packages,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.packages', [
                    'operator' => $operator,
                    'packages' => $packages,
                ]);
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, package $package)
    {
        $operator = $request->user();

        $this->authorize('update', $package);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.packages-edit', [
                    'package' => $package,
                ]);
                break;

            case 'operator':
                return view('admins.operator.packages-edit', [
                    'package' => $package,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.packages-edit', [
                    'package' => $package,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, package $package)
    {
        $this->authorize('update', $package);

        // validate
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'price' => 'integer|nullable',
            'operator_price' => 'integer|nullable',
            'visibility' => ['nullable', 'in:public,private'],
        ]);

        if ($request->filled('name')) {
            $this->authorize('updateName', $package);
            $package->name = $request->name;
        }

        if ($request->filled('price')) {
            $this->authorize('updatePrice', $package);
            $package->price = $request->price;
        }

        if ($request->filled('operator_price')) {
            $this->authorize('updateOperatorPrice', $package);
            $package->operator_price = $request->operator_price;
        }

        if ($request->filled('visibility')) {
            $package->visibility = $request->visibility;
        }

        if ($request->filled('dnd')) {
            $package->dnd = 1;
        }

        // price error
        if ($package->operator_price > $package->price) {

            return redirect()->route('packages.edit', ['package' => $package->id])->with('error', 'package price must be greater than operator price!');
        }

        $package->save();

        // name was changed
        if ($package->wasChanged('name')) {

            $where = [
                ['mgid', '=', $package->mgid],
                ['package_id', '=', $package->id],
            ];

            customer::where($where)->update(['package_name' => $package->name]);
        }

        // price was changed
        if ($package->wasChanged('price')) {
            $master_package = $package->master_package;
            $bills_where = [
                ['operator_id', '=', $package->operator_id],
                ['package_id', '=', $package->id],
                ['validity_period', '=', $master_package->validity],
            ];
            customer_bill::where($bills_where)->update(['amount' => $package->price]);
        }

        // operator_price was changed
        if ($package->wasChanged('operator_price')) {
            $master_package = $package->master_package;
            $bills_where = [
                ['operator_id', '=', $package->operator_id],
                ['package_id', '=', $package->id],
                ['validity_period', '=', $master_package->validity],
            ];
            customer_bill::where($bills_where)->update(['operator_amount' => $package->operator_price]);
        }

        if (MinimumConfigurationController::hasPendingConfig($request->user())) {
            return redirect()->route('configuration.next', ['operator' => $request->user()->id]);
        }

        if ($request->user()->id == $package->operator_id) {
            return redirect()->route('packages.index')->with('success', 'Package Updated Successfully!');
        }

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return redirect()->route('operators.master_packages.index', ['operator' => $package->operator->id])->with('success', 'Package Updated Successfully!');
                break;
            case 'operator':
                return redirect()->route('operators.packages.index', ['operator' => $package->operator->id])->with('success', 'Package Updated Successfully!');
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\package  $package
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, package $package)
    {
        $this->authorize('delete', $package);

        // delete child package
        foreach ($package->child_packages as $child_package) {
            if ($child_package->customer_count > 0) {
                return redirect(url()->previous())->with('success', 'The package is being used! Can not Delete!');
            } else {
                $child_package->delete();
            }
        }

        // delete the package
        $package->delete();

        return redirect(url()->previous())->with('success', 'Package has been deleted successfully!');
    }

    /**
     * Trial Package.
     *
     * @return \App\Models\package
     */
    public static function trialPackage(operator $operator)
    {
        // return if found Trial package
        $package = package::where('mgid', $operator->mgid)
            ->where('name', 'Trial')
            ->first();

        if ($package) {
            return $package;
        }

        // create and return new Trial package
        $master_package = new master_package();
        $master_package->mgid = $operator->mgid;
        $master_package->connection_type = 'Hotspot';
        $master_package->name = 'Trial';
        $master_package->rate_limit = 0;
        $master_package->rate_unit = 'M';
        $master_package->speed_controller = 'Radius_Server';
        $master_package->volume_limit = 100;
        $master_package->volume_unit = 'MB';
        $master_package->validity = 60;
        $master_package->validity_unit = 'Minute';
        $master_package->save();

        $package = new package();
        $package->mgid = $operator->mgid;
        $package->gid = $operator->mgid;
        $package->operator_id = $operator->mgid;
        $package->mpid = $master_package->id;
        $package->name = 'Trial';
        $package->price = 0;
        $package->operator_price = 0;
        $package->visibility = 'private';
        $package->save();
        $package->ppid = $package->id;
        $package->save();

        return $package;
    }

    /**
     * Get Package Price.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param  \App\Models\package  $package
     * @return int
     */
    public static function price(customer $customer, package $package)
    {
        if ($customer->connection_type !== 'PPPoE') {
            return $package->price;
        } else {
            if ($customer->billing_type === 'Daily') {
                return $package->price;
            }

            $where = [
                ['operator_id', '=', $customer->operator_id],
                ['customer_id', '=', $customer->id],
                ['package_id', '=', $package->id]
            ];
            $custom_price = custom_price::where($where)->firstOr(function () {
                return custom_price::make([
                    'id' => 0,
                    'price' => 0,
                ]);
            });

            if ($custom_price->price >  0) {
                return $custom_price->price;
            } else {
                return $package->price;
            }
        }
    }
}
