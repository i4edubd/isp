<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerSuspendController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;

class OperatorSuspendController extends Controller
{


    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, operator $operator)
    {
        if ($request->user()->id !== $operator->gid) {
            abort(403);
        }

        return view('admins.group_admin.operator-suspend', [
            'operator' => $operator,
        ]);
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
        if ($request->user()->id !== $operator->gid) {
            abort(403);
        }

        $operator->status = 'suspended';
        $operator->save();

        if ($request->filled('suspend_customers')) {

            $where = [
                ['mgid', '=', $operator->mgid],
                ['operator_id', '=', $operator->id],
                ['status', '=', 'active'],
            ];

            $customers = customer::where($where)->get();

            foreach ($customers as $customer) {

                $customer->suspend_reason = 'group_admin';
                $customer->save();

                $controller = new CustomerSuspendController();
                $controller->update($customer);
            }
        }

        return redirect()->route('operators.index')->with('success', 'Operator has been suspended successfully');
    }
}
