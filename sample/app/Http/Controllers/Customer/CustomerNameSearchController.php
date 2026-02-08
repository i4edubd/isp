<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerNameSearchController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $cache_key = 'names_of_' . $operator->id;

        $seconds = 300;

        return Cache::remember($cache_key, $seconds, function () use ($operator) {

            $names = [];

            switch ($operator->role) {

                case 'group_admin':
                    $customers = customer::where('mgid', $operator->id)->get();
                    break;

                case 'operator':
                    $customers = customer::where('operator_id', $operator->id)->get();
                    break;

                case 'sub_operator':
                    $customers = customer::where('operator_id', $operator->id)->get();
                    break;

                case 'manager':
                    $customers = customer::where('operator_id', $operator->group_admin->id)->get();
                    break;

                default:
                    return json_encode($names);
                    break;
            }

            foreach ($customers as $customer) {
                if (strlen($customer->name)) {
                    $names[] = $customer->name;
                }
            }

            return json_encode($names);
        });
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $customer_name
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $customer_name)
    {
        $operator = $request->user();

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $cache_key = 'customer_' . $operator_id . '_' . getVarName($customer_name);

        $seconds = 600;

        $customer = Cache::remember($cache_key, $seconds, function () use ($customer_name, $operator_id) {
            $where = [
                ['name', '=', $customer_name],
                ['operator_id', '=', $operator_id],
            ];
            return customer::where($where)->first();
        });

        if ($customer) {

            $this->authorize('viewDetails', $customer);

            return view('admins.components.customers-search-result', [
                'customer' => $customer,
            ]);
        } else {
            return 'Customer Not Found';
        }
    }
}
