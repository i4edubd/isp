<?php

namespace App\Http\Controllers;

use App\Models\all_customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ListMobilesForCardDistributorsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $card_distributor = $request->user('card');
        $cache_key = 'mobiles_of_' . $card_distributor->operator_id;
        $seconds = 600;
        return Cache::remember($cache_key, $seconds, function () use ($card_distributor) {
            $mobiles = [];
            $customers = all_customer::where('operator_id', $card_distributor->operator_id)->get();
            foreach ($customers as $customer) {
                $mobile = validate_mobile($customer->mobile, getCountryCode($customer->operator_id));
                if ($mobile) {
                    $mobiles[] = $customer->mobile;
                }
            }
            return json_encode($mobiles);
        });
    }
}
