<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerUsernameSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $cache_key = 'usernames_of_' . $operator->id;

        $seconds = 200;

        return Cache::remember($cache_key, $seconds, function () use ($operator) {

            $usernames = [];

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
                    return json_encode($usernames);
                    break;
            }

            foreach ($customers as $customer) {
                if (strlen($customer->username)) {
                    $usernames[] = $customer->username;
                }
            }

            return json_encode($usernames);
        });
    }


    /**
     * Display the specified resource.
     *
     * @param  string $customer_username
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $customer_username)
    {

        $operator = $request->user();

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        } else {
            $operator_id = $operator->id;
        }

        $cache_key = 'customer_' . $operator_id . '_' . getVarName($customer_username);

        $seconds = 200;

        $customer = Cache::remember($cache_key, $seconds, function () use ($customer_username) {
            return customer::where('username', $customer_username)->first();
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
