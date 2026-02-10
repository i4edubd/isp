<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\all_customer;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerMobileSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $cache_key = 'mobiles_of_' . $operator->id;

        $seconds = 300;

        return Cache::remember($cache_key, $seconds, function () use ($operator) {

            $mobiles = [];

            switch ($operator->role) {

                case 'group_admin':
                    $customers = all_customer::where('mgid', $operator->id)->get();
                    break;

                case 'operator':
                    $customers = all_customer::where('operator_id', $operator->id)->get();
                    break;

                case 'sub_operator':
                    $customers = all_customer::where('operator_id', $operator->id)->get();
                    break;

                case 'manager':
                    $customers = all_customer::where('operator_id', $operator->group_admin->id)->get();
                    break;

                default:
                    return json_encode($mobiles);
                    break;
            }

            foreach ($customers as $customer) {
                $mobile = validate_mobile($customer->mobile, getCountryCode($customer->operator_id));
                if ($mobile) {
                    $mobiles[] = $customer->mobile;
                }
            }

            return json_encode($mobiles);
        });
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $customer_mobile
     * @return \Illuminate\Http\Response
     */
    public function show(string $customer_mobile)
    {

        $cache_key = 'customer_' . $customer_mobile;

        $seconds = 600;

        $customer = Cache::remember($cache_key, $seconds, function () use ($customer_mobile) {
            return customer::where('mobile', $customer_mobile)->first();
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
