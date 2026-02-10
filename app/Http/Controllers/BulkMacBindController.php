<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerMacBindController;
use App\Models\Freeradius\radacct;
use Illuminate\Http\Request;

class BulkMacBindController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->filled('radacct_ids')) {
            $radacct_ids = $request->radacct_ids;
        } else {
            $radacct_ids = [];
        }

        if (count($radacct_ids) == 0) {
            return redirect()->route('online_customers.index');
        }

        $radaccts = radacct::with('customer')->whereIn('id', $radacct_ids)->get();

        foreach ($radaccts as $radacct) {
            CustomerMacBindController::create($radacct);
        }

        return redirect()->route('online_customers.index', ['refresh' => 1])->with('success', 'Done successfully');
    }
}
