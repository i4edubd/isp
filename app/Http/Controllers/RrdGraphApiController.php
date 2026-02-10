<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RrdGraphApiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return \Illuminate\Http\Response
     */
    public static function createDb(customer $customer)
    {
        $operator = operator::find($customer->operator_id);

        $ip = config('database.connections.' . $operator->node_connection . '.host');

        $url = 'http://' . $ip . '/api/rrd/create';

        return Http::asForm()->get($url, [
            'user_id' => encryptOrDecrypt('encrypt',  $customer->id),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return \Illuminate\Http\Response
     */
    public static function getImage(customer $customer)
    {
        $operator = operator::find($customer->operator_id);

        $ip = config('database.connections.' . $operator->node_connection . '.host');

        $url = 'http://' . $ip . '/api/rrd/image';

        Http::asForm()->get($url, [
            'user_id' => encryptOrDecrypt('encrypt',  $customer->id),
        ]);


        $image_url = 'http://' . $ip . '/storage/rrd-img/';

        $hourly_img = $image_url . 'h-graph' . $customer->id . '.png';
        $daily_img = $image_url . 'd-graph' . $customer->id . '.png';
        $weekly_img = $image_url . 'w-graph' . $customer->id . '.png';
        $monthly_img = $image_url . 'm-graph' . $customer->id . '.png';

        return collect([
            'hourly' => 'data:image/png;base64,' . base64_encode(file_get_contents($hourly_img)),
            "daily" => 'data:image/png;base64,' . base64_encode(file_get_contents($daily_img)),
            "weekly" => 'data:image/png;base64,' . base64_encode(file_get_contents($weekly_img)),
            "monthly" => 'data:image/png;base64,' . base64_encode(file_get_contents($monthly_img)),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return \Illuminate\Http\Response
     */
    public static function deleteRrd(customer $customer)
    {
        $operator = operator::find($customer->operator_id);

        $ip = config('database.connections.' . $operator->node_connection . '.host');

        $url = 'http://' . $ip . '/api/rrd/delete';

        Http::asForm()->get($url, [
            'user_id' => encryptOrDecrypt('encrypt',  $customer->id),
        ]);
    }
}
