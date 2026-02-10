<?php

namespace App\Http\Controllers;

use App\Models\billing_profile;
use App\Models\customer_zone;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class DownloadusersDownloadableController extends Controller
{

    /**
     * The Fields can be downloaded.
     *
     * @return array
     */
    public static function downloadable()
    {
        return [
            'connection_type' => 'Connection Type',
            'zone_id' => 'Zone',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'billing_profile_id' => 'Billing Profile',
            'username' => 'Username',
            'password' => 'Password',
            'package_name' => 'Package',
            'package_expired_at' => 'Package Expiration Time',
            'payment_status' => 'Payment Status',
            'status' => 'Status',
            'login_ip' => 'IPv4 Address',
            'login_mac_address' => 'Mac Address',
            'house_no' => 'House No',
            'road_no' => 'Road No',
            'thana' => 'Thana',
            'district' => 'District',
            'type_of_client' => 'Type of client',
            'type_of_connection' => 'Type of Connection',
            'type_of_connectivity' => 'Type of connectivity',
            'registration_date' => 'Registration Date',
            'comment' => 'Comment',
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
                return view('admins.group_admin.download-users-downloadable', [
                    'downloadable_fields' => self::downloadable(),
                ]);
                break;

            case 'operator':
                return view('admins.operator.download-users-downloadable', [
                    'downloadable_fields' => self::downloadable(),
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.download-users-downloadable', [
                    'downloadable_fields' => self::downloadable(),
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

        $downloadable = self::downloadable();

        if ($request->filled('downloadable_fields')) {
            $requested_fields = [];
            foreach ($request->downloadable_fields as $downloadable_field) {
                $requested_fields[$downloadable_field] = $downloadable[$downloadable_field];
            }
        } else {
            $requested_fields = $downloadable;
        }

        $select_fields = array_keys($requested_fields);

        $writer = SimpleExcelWriter::streamDownload('customers-info.xlsx');

        foreach ($customers as $customer) {

            $customer = $customer->toArray();

            $downloadable_row = [];

            $downloadable_row["Customer ID"] = $customer['id'];

            foreach ($select_fields as $field) {

                switch ($field) {
                    case 'zone_id':
                        $zone = customer_zone::where('id', $customer['zone_id'])->firstOr(function () {
                            return
                                customer_zone::make([
                                    'name' => 'Not Found',
                                ]);
                        });
                        $downloadable_row[$downloadable[$field]] = $zone->name;
                        break;
                    case 'billing_profile_id':
                        $billing_profile = billing_profile::where('id', $customer['billing_profile_id'])->firstOr(function () {
                            return
                                billing_profile::make([
                                    'profile_name' => 'N/A',
                                ]);
                        });
                        $downloadable_row[$downloadable[$field]] = $billing_profile->name;
                        break;
                    default:
                        $downloadable_row[$downloadable[$field]] = $customer[$field];
                        break;
                }
            }

            $writer->addRow($downloadable_row);
        }

        $writer->toBrowser();
    }
}
