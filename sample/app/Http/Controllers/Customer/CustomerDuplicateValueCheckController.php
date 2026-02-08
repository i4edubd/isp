<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\all_customer;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class CustomerDuplicateValueCheckController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'attribute' => 'required',
            'value' => 'required',
        ]);

        $duplicate = 0;

        if ($request->attribute == 'mobile') {

            $mobile = validate_mobile($request->value);

            if ($mobile == 0) {
                return 'Invalid Number';
            }

            $duplicate = all_customer::where($request->attribute, $mobile)->count();
        } else {

            $duplicate = customer::where($request->attribute, $request->value)->count();
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => $duplicate,
        ]);
    }
}
