<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerPackageUpdateController;
use App\Models\all_customer;
use App\Models\custom_price;
use App\Models\customer_bill;
use App\Models\customer_complain;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\ipv4address;
use App\Models\operator;
use App\Models\package;
use App\Models\pgsql\pgsql_customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OperatorChangeController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $this->authorize('changeOperator', $customer);

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                $operators = $operator->operators->where('role', '!=', 'manager');
                return view('admins.group_admin.customers-operator-edit', [
                    'customer' => $customer,
                    'operators' => $operators,
                ]);
                break;

            case 'operator':
                $operators = $operator->operators->where('role', '!=', 'manager');
                return view('admins.operator.customers-operator-edit', [
                    'customer' => $customer,
                    'operators' => $operators,
                ]);
                break;
        }
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
        $this->authorize('changeOperator', $customer);

        $request->validate([
            'operator_id' => 'required|numeric',
        ]);

        if ($request->operator_id == $customer->operator_id) {
            return redirect()->route('customers.index')->with('success', 'Same Operator');
        }

        $new_operator = operator::findOrFail($request->operator_id);

        self::changeOperator($customer, $new_operator);

        $url = route('customers.index') . '?refresh=1';

        return redirect($url)->with('success', 'Operator Changed Successfully!');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\operator $new_operator
     * @return int
     */
    public static function changeOperator(customer $customer, operator $new_operator)
    {
        all_customer::where('mgid', $customer->mgid)
            ->where('customer_id', $customer->id)
            ->update(['operator_id' => $new_operator->id]);

        customer_bill::where('mgid', $customer->mgid)
            ->where('customer_id', $customer->id)
            ->update(['gid' => $new_operator->gid, 'operator_id' => $new_operator->id]);

        customer_complain::where('operator_id', $customer->operator_id)
            ->where('customer_id', $customer->id)
            ->update(['operator_id' => $new_operator->id]);

        custom_price::where('mgid', $customer->mgid)
            ->where('customer_id', $customer->id)
            ->update(['operator_id' => $new_operator->id]);

        ipv4address::where('operator_id', $customer->operator_id)
            ->where('customer_id', $customer->id)
            ->update(['operator_id' => $new_operator->id]);

        // << pgsql_connection
        $pgsql_customer = new pgsql_customer();
        $pgsql_customer->setConnection($new_operator->pgsql_connection);
        $pgsql_customer->where('mgid', $customer->mgid)
            ->where('customer_id', $customer->id)
            ->update(['operator_id' => $new_operator->id]);
        // pgsql_connection >>

        $radacct = new radacct();
        $radacct->setConnection($new_operator->radius_db_connection);
        $radacct->where('mgid', $customer->mgid)
            ->where('username', $customer->username)
            ->update(['operator_id' => $new_operator->id]);

        $customer->gid = $new_operator->gid;
        $customer->operator_id = $new_operator->id;
        $customer->save();

        // change package
        $from_package = package::find($customer->package_id);

        if ($from_package) {

            $to_where = [
                ['operator_id', '=', $new_operator->id],
                ['mpid', '=', $from_package->mpid],
            ];

            $to_package = package::where($to_where)->firstOr(function () use ($new_operator, $from_package) {

                if ($from_package->name == 'Trial') {
                    return false;
                }

                if ($new_operator->role == 'group_admin' || $new_operator->role == 'operator') {
                    $to_package = new package();
                    $to_package->mgid = $new_operator->mgid;
                    $to_package->gid = $new_operator->gid;
                    $to_package->operator_id = $new_operator->id;
                    $to_package->mpid = $from_package->mpid;
                    $to_package->name = $from_package->name;
                    $to_package->price = $from_package->price;
                    $to_package->operator_price = $from_package->operator_price;
                    $to_package->visibility = $from_package->visibility;
                    $to_package->save();
                    $to_package->ppid = $to_package->id;
                    $to_package->save();
                    return $to_package;
                }

                if ($new_operator->role == 'sub_operator') {
                    $ppackage = new package();
                    $ppackage->mgid = $new_operator->mgid;
                    $ppackage->gid = $new_operator->gid;
                    $ppackage->operator_id = $new_operator->gid;
                    $ppackage->mpid = $from_package->mpid;
                    $ppackage->name = $from_package->name;
                    $ppackage->price = $from_package->price;
                    $ppackage->operator_price = $from_package->operator_price;
                    $ppackage->visibility = $from_package->visibility;
                    $ppackage->save();
                    $ppackage->ppid = $ppackage->id;
                    $ppackage->save();

                    $to_package = new package();
                    $to_package->mgid = $new_operator->mgid;
                    $to_package->gid = $new_operator->gid;
                    $to_package->operator_id = $new_operator->id;
                    $to_package->mpid = $from_package->mpid;
                    $to_package->ppid = $ppackage->id;
                    $to_package->name = $from_package->name;
                    $to_package->price = $from_package->price;
                    $to_package->operator_price = $from_package->operator_price;
                    $to_package->visibility = $from_package->visibility;
                    $to_package->save();
                    return $to_package;
                }
            });

            if ($to_package) {
                if (Gate::forUser($customer)->allows('usePackage', [$to_package])) {
                    CustomerPackageUpdateController::update($customer, $to_package);
                }
            }
        }

        return 0;
    }
}
