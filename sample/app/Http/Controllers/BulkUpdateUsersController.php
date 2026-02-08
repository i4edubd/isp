<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class BulkUpdateUsersController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.bulk-update-users');
                break;

            case 'operator':
                return view('admins.operator.bulk-update-users');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.bulk-update-users');
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $file = $request->file('update_info')->store('bulk_update');

        $path = Storage::path($file);

        $rows = SimpleExcelReader::create($path)->getRows()->toArray();

        foreach ($rows as $row) {

            if (array_key_exists('id', $row) == false) {
                return redirect()->route('bulk-update-users.create')->with('error', 'Update Failed! Please follow the instruction');
            }

            $customer = customer::findOrFail($row['id']);

            $this->authorize('update', $customer);

            // mobile
            if (array_key_exists('mobile', $row)) {
                $mobile = validate_mobile($row['mobile'], getCountryCode($request->user()->id));
                if ($mobile) {
                    try {
                        $customer->mobile = $mobile;
                        $customer->save();
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
            }

            // name
            if (array_key_exists('name', $row)) {
                $customer->name = $row['name'];
            }

            // email
            if (array_key_exists('email', $row)) {
                $customer->email = $row['email'];
            }

            // house_no
            if (array_key_exists('house_no', $row)) {
                $customer->house_no = $row['house_no'];
            }

            // road_no
            if (array_key_exists('road_no', $row)) {
                $customer->road_no = $row['road_no'];
            }

            // thana
            if (array_key_exists('thana', $row)) {
                $customer->thana = $row['thana'];
            }

            // district
            if (array_key_exists('district', $row)) {
                $customer->district = $row['district'];
            }

            // type_of_client
            if (array_key_exists('type_of_client', $row)) {
                $customer->type_of_client = $row['type_of_client'];
            }

            // type_of_connection
            if (array_key_exists('type_of_connection', $row)) {
                $customer->type_of_connection = $row['type_of_connection'];
            }

            // type_of_connectivity
            if (array_key_exists('type_of_connectivity', $row)) {
                $customer->type_of_connectivity = $row['type_of_connectivity'];
            }

            $customer->save();

            AllCustomerController::updateOrCreate($customer);
        }

        return redirect()->route('customers.index')->with('success', 'Customers are updated successfully');
    }
}
