<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class DownloadusersUploadableController extends Controller
{

    /**
     * The Fields can be uploaded.
     *
     * @return array
     */
    public static function uploadable()
    {

        return [
            'name' => 'Name',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'house_no' => 'House No',
            'road_no' => 'Road No',
            'thana' => 'Thana',
            'district' => 'District',
            'type_of_client' => 'Type of client',
            'type_of_connection' => 'Type of Connection',
            'type_of_connectivity' => 'Type of connectivity',
        ];
    }

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
                return view('admins.group_admin.download-users-uploadable', [
                    'downloadable_fields' => self::uploadable(),
                ]);
                break;

            case 'operator':
                return view('admins.operator.download-users-uploadable', [
                    'downloadable_fields' => self::uploadable(),
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.download-users-uploadable', [
                    'downloadable_fields' => self::uploadable(),
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
    public function store(Request $request)
    {
        $request->validate([
            'connection_type' => 'nullable|in:PPPoE,Hotspot,StaticIp,Other',
            'status' => 'nullable|in:active,suspended,disabled',
            'payment_status' => 'nullable|in:billed,paid',
            'zone_id' => 'nullable|numeric',
            'package_id' => 'nullable|numeric',
            'mac_bind' => 'nullable|numeric',
            'operator_id' => 'nullable|numeric',
        ]);

        $operator = $request->user();

        $filter = [];

        $filter[] = ['gid', '=', $operator->gid];

        if ($request->filled('connection_type')) {
            $filter[] = ['connection_type', '=', $request->connection_type];
        }

        if ($request->filled('status')) {
            $filter[] = ['status', '=', $request->status];
        }

        if ($request->filled('payment_status')) {
            $filter[] = ['payment_status', '=', $request->payment_status];
        }

        if ($request->filled('zone_id')) {
            $filter[] = ['zone_id', '=', $request->zone_id];
        }

        if ($request->filled('package_id')) {
            $filter[] = ['package_id', '=', $request->package_id];
        }

        if ($request->filled('year')) {
            $filter[] = ['registration_year', '=', $request->year];
        }

        if ($request->filled('month')) {
            $filter[] = ['registration_month', '=', $request->month];
        }

        if ($request->filled('operator_id')) {
            if ($request->operator_id == 0) {
                $customers = customer::where('mgid', $request->user()->id)->where($filter)->get();
            } else {
                $customers = customer::where('operator_id', $request->operator_id)->where($filter)->get();
            }
        } else {
            $customers = customer::where('operator_id', $request->user()->id)->where($filter)->get();
        }

        $customers = $customers->filter(function ($value, $key) use ($operator) {
            return $operator->can('viewDetails', $value);
        });

        if (count($customers) == 0) {
            return redirect()->route('customers.index')->with('info', 'Nothing to Download');
        }

        // sorting
        if ($request->filled('sortby')) {
            $customers = $customers->sortBy($request->sortby);
        }

        $downloadable = self::uploadable();

        if ($request->filled('downloadable_fields')) {

            $requested_fields = [];

            foreach ($request->downloadable_fields as $downloadable_field) {
                $requested_fields[$downloadable_field] = $downloadable[$downloadable_field];
            }
        } else {

            $requested_fields = $downloadable;
        }

        $select_fields = array_keys($requested_fields);

        $writer = SimpleExcelWriter::streamDownload('edit_customer_info.xlsx');

        foreach ($customers as $customer) {

            $customer = $customer->toArray();

            $downloadable_row = [];

            $downloadable_row["id"] = $customer['id'];

            $downloadable_row["username"] = $customer['username'];

            foreach ($select_fields as $field) {
                $downloadable_row[$field] = $customer[$field];
            }

            $writer->addRow($downloadable_row);
        }

        $writer->toBrowser();
    }
}
