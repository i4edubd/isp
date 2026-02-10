<?php

namespace App\Http\Controllers;

use App\Models\mandatory_customers_attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MandatoryCustomersAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers_attributes = config('customers_attributes');

        return view('admins.group_admin.mandatory-customers-attributes', ['customers_attributes' => $customers_attributes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customers_attributes = config('customers_attributes');
        foreach ($customers_attributes as $key => $value) {
            $request->validate([
                $key => 'required|in:Yes,No',
            ]);
        }

        mandatory_customers_attribute::where('mgid', $request->user()->mgid)->delete();

        foreach ($customers_attributes as $key => $value) {
            if ($request->$key == 'Yes') {
                $mandatory_customers_attribute = new mandatory_customers_attribute();
                $mandatory_customers_attribute->mgid = $request->user()->mgid;
                $mandatory_customers_attribute->attribute = $key;
                $mandatory_customers_attribute->save();
            }
        }

        $cache_key = 'app_models_mandatory_customers_attribute_' . $request->user()->mgid;
        if (Cache::has($cache_key)) {
            Cache::forget($cache_key);
        }

        return redirect()->route('mandatory_customers_attributes.index')->with('info', 'Updated Successfully');
    }
}
