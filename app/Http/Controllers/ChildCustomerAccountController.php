<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class ChildCustomerAccountController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $this->authorize('makeChild', $customer);

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.make-child', [
                    'customer' => $customer,
                ]);
                break;

            case 'operator':
                return view('admins.operator.make-child', [
                    'customer' => $customer,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.make-child', [
                    'customer' => $customer,
                ]);
                break;

            case 'manager':
                return view('admins.manager.make-child', [
                    'customer' => $customer,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer $customer)
    {
        $request->validate([
            'parent_id' => 'required',
        ]);

        $parent = customer::where('id', $request->parent_id)->orWhere('username', $request->parent_id)->firstOrFail();

        $this->authorize('addChild', [$parent]);

        self::makeChild($parent, $customer);

        return redirect()->route('customers.index')->with('info', 'done successfully');
    }

    /**
     * Make Childs Parent On Delete Parent
     * 
     * @param  \App\Models\Freeradius\customer $parent
     * @return \Illuminate\Http\Response
     */
    public static function makeChildsParentOnDeleteParent(customer $parent)
    {
        if ($parent->id !== $parent->parent_id) {
            return 0;
        }

        $childs = $parent->childAccounts;
        foreach ($childs as $child) {
            self::makeParent($child);
        }

        return 'Done';
    }

    /**
     * Make Child
     * 
     * @param  \App\Models\Freeradius\customer $parent
     * @param  \App\Models\Freeradius\customer $customer
     * @return \Illuminate\Http\Response
     */
    public static function makeChild(customer $parent, customer $customer)
    {
        $customer->parent_id = $parent->id;
        $customer->save();
        return 'Done';
    }

    /**
     * Make Parent
     *
     * @param  \App\Models\Freeradius\customer $child
     * @return \Illuminate\Http\Response
     */
    public static function makeParent(customer $child)
    {
        $child->parent_id = $child->id;
        $child->save();
        return 'Done';
    }
}
