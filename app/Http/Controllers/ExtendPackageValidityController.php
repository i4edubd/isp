<?php

namespace App\Http\Controllers;

use App\Jobs\ExtendPackageValidityJob;
use App\Models\billing_profile;
use App\Models\extend_package_validity;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExtendPackageValidityController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, operator $operator)
    {
        $authorized = 0;

        $requester = $request->user();

        if ($requester->id == $operator->id) {
            $authorized = 1;
        }

        if ($requester->id == $operator->gid) {
            $authorized = 1;
        }

        if ($requester->id == $operator->mgid) {
            $authorized = 1;
        }

        if ($authorized == 0) {
            abort(403);
        }

        $extend_requests = extend_package_validity::where('operator_id', $operator->id)->get();

        // sanitize
        $extend_requests = $extend_requests->filter(function ($extend_request) {

            $package = CacheController::getPackage($extend_request->package_id);

            if (!$package) {
                $extend_request->delete();
                return false;
            }

            if ($package->price < 2) {
                $extend_request->delete();
                return false;
            }

            if ($extend_request->connection_type == 'Other') {
                $extend_request->delete();
                return false;
            }

            if ($extend_request->connection_type == 'StaticIp') {
                $extend_request->delete();
                return false;
            }

            if ($extend_request->connection_type == 'Hotspot') {
                return true;
            }

            if ($extend_request->connection_type == 'PPPoE') {

                $billing_profile = CacheController::getBillingProfile($extend_request->billing_profile_id);

                if (!$billing_profile) {
                    $extend_request->delete();
                    return false;
                }

                if ($billing_profile->billing_type !== 'Daily') {
                    $extend_request->delete();
                    return false;
                }

                return true;
            }
        });

        // Nothing to process
        if ($extend_requests->count() == 0) {
            extend_package_validity::where('operator_id', $operator->id)->delete();
            return redirect()->route('customers.index')->with('error', 'Bulk package validity extends is applicable for hotspot customers and ppp customers using the daily billing only.');
        }

        // unique  packages
        $extend_requests = $extend_requests->unique('package_id');

        // generate invoice
        $rows = [];
        $total_customers_amount = 0;
        $total_operators_amount = 0;
        foreach ($extend_requests as $extend_request) {
            $row = [];
            $package = package::find($extend_request->package_id);
            $row['package_name'] = $package->name;
            $where = [
                ['operator_id', '=', $operator->id],
                ['package_id', '=', $package->id],
            ];
            $row['customer_count'] = extend_package_validity::where($where)->count();
            $row['validity'] = $extend_request->validity;
            $row['customers_amount'] = ($package->price / $package->master_package->validity) * $row['validity'] * $row['customer_count'];
            $row['operators_amount'] = ($package->operator_price / $package->master_package->validity) * $row['validity'] * $row['customer_count'];
            $total_customers_amount = $total_customers_amount + $row['customers_amount'];
            $total_operators_amount = $total_operators_amount + $row['operators_amount'];
            $rows[] = collect($row);
        }

        $bills = collect($rows);

        // warning
        if ($operator->account_type == 'debit') {
            $account_balance = $operator->account_balance;
        } else {
            if ($operator->credit_limit > 0) {
                $account_balance = $operator->creditBalance;
            } else {
                $account_balance = $total_operators_amount;
            }
        }
        if ($account_balance < $total_operators_amount) {
            $warning = 1;
        } else {
            $warning = 0;
        }

        switch ($requester->role) {

            case 'group_admin':
                return view('admins.group_admin.extend_package_validity', [
                    'operator' => $operator,
                    'bills' => $bills,
                    'total_customers_amount' => $total_customers_amount,
                    'total_operators_amount' => $total_operators_amount,
                    'account_balance' => $account_balance,
                    'warning' => $warning,
                ]);
                break;

            case 'operator':
                return view('admins.operator.extend_package_validity', [
                    'operator' => $operator,
                    'bills' => $bills,
                    'total_customers_amount' => $total_customers_amount,
                    'total_operators_amount' => $total_operators_amount,
                    'account_balance' => $account_balance,
                    'warning' => $warning,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.extend_package_validity', [
                    'operator' => $operator,
                    'bills' => $bills,
                    'total_customers_amount' => $total_customers_amount,
                    'total_operators_amount' => $total_operators_amount,
                    'account_balance' => $account_balance,
                    'warning' => $warning,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        $authorized = 0;

        $requester = $request->user();

        if ($requester->id == $operator->id) {
            $authorized = 1;
        }

        if ($requester->id == $operator->gid) {
            $authorized = 1;
        }

        if ($requester->id == $operator->mgid) {
            $authorized = 1;
        }

        if ($authorized == 0) {
            abort(403);
        }

        ExtendPackageValidityJob::dispatch($operator)
            ->onConnection('database')
            ->onQueue('extend_package_validity');

        return redirect()->route('customers.index')->with('success', 'Job is processing');
    }
}
