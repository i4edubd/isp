<?php

namespace App\Http\Controllers;

use App\Models\customer_bill;
use App\Models\customer_bills_summary;
use App\Models\customer_zone;
use App\Models\operator;
use App\Models\package;
use Illuminate\Http\Request;

class CustomerBillsSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $bills_summaries = customer_bills_summary::where('operator_id', $operator->id)->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customers-bills-summary', [
                    'bills_summaries' => $bills_summaries,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-bills-summary', [
                    'bills_summaries' => $bills_summaries,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-bills-summary', [
                    'bills_summaries' => $bills_summaries,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        self::store($operator);

        return redirect()->route('customer_bills_summary.index')->with('success', 'Report has been generated Successfully!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\operator $operator
     * @return int
     */
    public static function store(operator $operator)
    {
        $allowed_roles = ['group_admin', 'operator', 'sub_operator'];

        $requested_role = $operator->role;

        if (in_array($requested_role, $allowed_roles) == false) {
            return 0;
        }

        // clean previous report
        customer_bills_summary::where('operator_id', $operator->id)->delete();

        // direct
        $customer_bills = customer_bill::where('operator_id', $operator->id)->get();

        $customer_bills = $customer_bills->groupBy('package_id');

        foreach ($customer_bills as $package_id => $bills) {
            $package = package::find($package_id);
            $bill_count = $bills->count();

            $bills_summary = new customer_bills_summary();
            $bills_summary->operator_id = $operator->id;
            $bills_summary->type = 'direct';
            $bills_summary->package_id = $package_id;
            $bills_summary->bill_count = $bill_count;
            $bills_summary->package_price = $package->price;
            $bills_summary->subtotal = $package->price * $bill_count;
            $bills_summary->save();
        }

        // resell
        if ($operator->role == 'operator') {
            $resellers  = operator::where('role', 'sub_operator')
                ->where('gid', $operator->id)
                ->get();

            foreach ($resellers as $reseller) {
                $reseller_bills = customer_bill::where('gid', $operator->id)
                    ->where('operator_id', $reseller->id)
                    ->get();

                $reseller_bills = $reseller_bills->groupBy('package_id');

                foreach ($reseller_bills as $package_id => $bills) {
                    $package = package::find($package_id);
                    $bill_count = $bills->count();

                    $bills_summary = new customer_bills_summary();
                    $bills_summary->operator_id = $operator->id;
                    $bills_summary->type = 'resell';
                    $bills_summary->reseller_id = $reseller->id;
                    $bills_summary->package_id = $package_id;
                    $bills_summary->bill_count = $bill_count;
                    $bills_summary->package_price = $package->operator_price;
                    $bills_summary->subtotal = $package->operator_price * $bill_count;
                    $bills_summary->save();
                }
            }
        }

        // resell & sub_resell
        if ($operator->role == 'group_admin') {

            // resell
            $resellers  = operator::where('role', 'operator')
                ->where('gid', $operator->id)
                ->get();

            foreach ($resellers as $reseller) {
                $reseller_bills = customer_bill::where('gid', $operator->id)
                    ->where('operator_id', $reseller->id)
                    ->get();

                $reseller_bills = $reseller_bills->groupBy('package_id');

                foreach ($reseller_bills as $package_id => $bills) {
                    $package = package::find($package_id);
                    $bill_count = $bills->count();

                    $bills_summary = new customer_bills_summary();
                    $bills_summary->operator_id = $operator->id;
                    $bills_summary->type = 'resell';
                    $bills_summary->reseller_id = $reseller->id;
                    $bills_summary->package_id = $package_id;
                    $bills_summary->bill_count = $bill_count;
                    $bills_summary->package_price = $package->operator_price;
                    $bills_summary->subtotal = $package->operator_price * $bill_count;
                    $bills_summary->save();
                }

                // sub_resell
                $sub_resellers = operator::where('role', 'sub_operator')
                    ->where('gid', $reseller->id)
                    ->where('mgid', $operator->id)
                    ->get();

                foreach ($sub_resellers as $sub_reseller) {

                    $sub_reseller_bills = customer_bill::where('mgid', $operator->id)
                        ->where('gid', $reseller->id)
                        ->where('operator_id', $sub_reseller->id)
                        ->get();

                    $sub_reseller_bills = $sub_reseller_bills->groupBy('package_id');

                    foreach ($sub_reseller_bills as $package_id => $bills) {
                        $package = package::find($package_id);
                        $parent_package = $package->parent_package;
                        $bill_count = $bills->count();

                        $bills_summary = new customer_bills_summary();
                        $bills_summary->operator_id = $operator->id;
                        $bills_summary->type = 'sub_resell';
                        $bills_summary->reseller_id = $reseller->id;
                        $bills_summary->sub_reseller_id = $sub_reseller->id;
                        $bills_summary->package_id = $parent_package->id;
                        $bills_summary->bill_count = $bill_count;
                        $bills_summary->package_price = $parent_package->operator_price;
                        $bills_summary->subtotal = $bill_count * $parent_package->operator_price;
                        $bills_summary->save();
                    }
                }
            }
        }

        // to_operator
        if ($operator->role == 'sub_operator') {

            $customer_bills = customer_bill::where('operator_id')->get();

            $customer_bills = $customer_bills->groupBy('package_id');

            foreach ($customer_bills as $package_id => $bills) {

                $package = package::find($package_id);
                $bill_count = $bills->count();

                $bills_summary = new customer_bills_summary();
                $bills_summary->operator_id = $operator->id;
                $bills_summary->type = 'to_operator';
                $bills_summary->reseller_id = $operator->group_admin->id;
                $bills_summary->package_id = $package->id;
                $bills_summary->bill_count = $bill_count;
                $bills_summary->package_price = $package->operator_price;
                $bills_summary->subtotal = $bill_count * $package->operator_price;
                $bills_summary->save();
            }
        }

        // to_group_admin
        if ($operator->role == 'operator') {

            $reseller_bills = customer_bill::where('operator_id', $operator->id)->get();

            $reseller_bills = $reseller_bills->groupBy('package_id');

            foreach ($reseller_bills as $package_id => $bills) {
                $package = package::find($package_id);
                $bill_count = $bills->count();

                $bills_summary = new customer_bills_summary();
                $bills_summary->operator_id = $operator->id;
                $bills_summary->type = 'to_group_admin';
                $bills_summary->reseller_id = $operator->group_admin->id;
                $bills_summary->package_id = $package_id;
                $bills_summary->bill_count = $bill_count;
                $bills_summary->package_price = $package->operator_price;
                $bills_summary->subtotal = $package->operator_price * $bill_count;
                $bills_summary->save();
            }

            // sub_resell
            $sub_resellers = operator::where('role', 'sub_operator')
                ->where('gid', $operator->id)
                ->get();

            foreach ($sub_resellers as $sub_reseller) {

                $sub_reseller_bills = customer_bill::where('gid', $operator->id)
                    ->where('operator_id', $sub_reseller->id)
                    ->get();

                $sub_reseller_bills = $sub_reseller_bills->groupBy('package_id');

                foreach ($sub_reseller_bills as $package_id => $bills) {
                    $package = package::find($package_id);
                    $parent_package = $package->parent_package;
                    $bill_count = $bills->count();

                    $bills_summary = new customer_bills_summary();
                    $bills_summary->operator_id = $operator->id;
                    $bills_summary->type = 'to_group_admin';
                    $bills_summary->reseller_id = $operator->group_admin->id;
                    $bills_summary->package_id = $parent_package->id;
                    $bills_summary->bill_count = $bill_count;
                    $bills_summary->package_price = $parent_package->operator_price;
                    $bills_summary->subtotal = $bill_count * $parent_package->operator_price;
                    $bills_summary->save();
                }
            }
        }

        return 0;
    }
}
