<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\custom_field;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\customer_custom_attribute;
use Illuminate\Http\Request;

class CustomerCustomAttributeController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {

        $operator = $request->user();

        $custom_fields = $operator->custom_fields;

        $collections = [];

        foreach ($custom_fields as $custom_field) {

            $value = '';

            $collection = [];

            $collection['id'] = $custom_field->id;

            $collection['operator_id'] = $custom_field->operator_id;

            $collection['name'] = $custom_field->name;

            $where = [
                ['customer_id', '=', $customer->id],
                ['custom_field_id', '=', $custom_field->id],
            ];

            if (customer_custom_attribute::where($where)->count()) {
                $value = customer_custom_attribute::where($where)->first()->value;
            }

            $collection['value'] = $value;

            $collections[] = custom_field::make($collection);
        }


        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customer-custom-attribute-create', [
                    'customer' => $customer,
                    'custom_fields' => $collections,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customer-custom-attribute-create', [
                    'customer' => $customer,
                    'custom_fields' => $collections,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customer-custom-attribute-create', [
                    'customer' => $customer,
                    'custom_fields' => $collections,
                ]);
                break;
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer $customer)
    {
        //delete previous records
        customer_custom_attribute::where('customer_id', $customer->id)->delete();

        //Add New Records
        $operator = $request->user();

        $custom_fields = $operator->custom_fields;

        foreach ($custom_fields as $custom_field) {
            if ($request->filled($custom_field->id)) {
                $customer_custom_attribute = new customer_custom_attribute();
                $customer_custom_attribute->customer_id = $customer->id;
                $customer_custom_attribute->custom_field_id = $custom_field->id;
                $customer_custom_attribute->value =  $request->input($custom_field->id);
                $customer_custom_attribute->save();
            }
        }

        //show customer profile
        return redirect()->route('customers.index');
    }
}
