<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\enum\PaymentPurpose;
use App\Http\Controllers\NewCustomersPaymentController;
use App\Models\all_customer;
use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\customer_custom_attribute;
use App\Models\temp_customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerCreateController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\temp_customer  $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, temp_customer $temp_customer)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customers-create', [
                    'temp_customer' => $temp_customer,
                    'custom_fields' => $operator->custom_fields,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-create', [
                    'temp_customer' => $temp_customer,
                    'custom_fields' => $operator->custom_fields,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-create', [
                    'temp_customer' => $temp_customer,
                    'custom_fields' => $operator->custom_fields,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-create', [
                    'temp_customer' => $temp_customer,
                    'custom_fields' => $operator->group_admin->custom_fields,
                ]);
                break;
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\temp_customer  $temp_customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, temp_customer $temp_customer)
    {
        $customer = new customer();

        //General Information
        $customer->house_no = $request->house_no;
        $customer->road_no = $request->road_no;
        $customer->thana = $request->thana;
        $customer->district = $request->district;
        $customer->type_of_client = $request->type_of_client;
        $customer->type_of_connection = $request->type_of_connection;
        $customer->type_of_connectivity = $request->type_of_connectivity;

        //Registration timestamp
        $customer->registration_date = date(config('app.date_format'));
        $customer->registration_week = date(config('app.week_format'));
        $customer->registration_month = date(config('app.month_format'));
        $customer->registration_year = date(config('app.year_format'));

        //Import from temp_customer
        $customer->parent_id = $temp_customer->parent_id;
        $customer->mgid = $temp_customer->mgid;
        $customer->gid = $temp_customer->gid;
        $customer->operator_id = $temp_customer->operator_id;
        $customer->company = $temp_customer->company;
        $customer->connection_type = $temp_customer->connection_type;
        $customer->billing_type = $temp_customer->billing_type;
        $customer->zone_id = $temp_customer->zone_id;
        $customer->device_id = $temp_customer->device_id;
        $customer->name = $temp_customer->name;
        $customer->mobile = $temp_customer->mobile;
        $customer->verified_mobile = 1;
        $customer->email = $temp_customer->email;
        $customer->billing_profile_id = $temp_customer->billing_profile_id;
        $customer->username = trim($temp_customer->username);
        $customer->password = trim($temp_customer->password);
        $customer->package_id = $temp_customer->package_id;
        $customer->package_name = $temp_customer->package_name;
        $customer->package_started_at = $temp_customer->package_started_at;
        $customer->package_expired_at = $temp_customer->package_expired_at;
        $customer->exptimestamp =  Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
        $customer->rate_limit = $temp_customer->rate_limit;
        $customer->total_octet_limit = $temp_customer->total_octet_limit;
        $customer->router_ip = $temp_customer->router_ip;
        $customer->router_id = $temp_customer->router_id;
        $customer->login_ip = $temp_customer->login_ip;
        $customer->status = 'active';
        $customer->payment_status = 'paid';
        $customer->nid = $temp_customer->nid;
        $customer->date_of_birth = $temp_customer->date_of_birth;

        //username &  password for Hotspot & StaticIp customers
        if ($temp_customer->connection_type !== 'PPPoE') {
            $customer->username = $temp_customer->mobile;
            $customer->password = $temp_customer->mobile;
        }

        $customer->save();

        if ($customer->parent_id == 0) {
            $customer->parent_id = $customer->id;
            $customer->save();
        }

        //Central customer information
        AllCustomerController::updateOrCreate($customer);

        //radcheck and radreply information
        if ($customer->connection_type == 'PPPoE') {

            PPPoECustomersRadAttributesController::updateOrCreate($customer);

            if ($temp_customer->sms_password == 1) {
                SmsMessagesForCustomerController::pppRegistration($customer);
            }
        }

        if ($customer->connection_type == 'Hotspot') {
            HotspotCustomersRadAttributesController::updateOrCreate($customer);
        }

        // Do Accounting
        switch ($customer->connection_type) {
            case 'PPPoE':
                if ($customer->billing_type == 'Daily') {
                    NewCustomersPaymentController::store($customer);
                }
                if ($customer->billing_type == 'Monthly') {
                    $validity = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_started_at, getTimeZone($customer->operator_id), 'en')->diffInDays(Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')) + 1;
                    $billing_profile = billing_profile::find($customer->billing_profile_id);
                    $validity = $validity - $billing_profile->grace_period;

                    // reset validity since payment is due and need to pay by today
                    $customer->package_expired_at = Carbon::now(getTimeZone($customer->operator_id))->isoFormat(config('app.expiry_time_format'));
                    $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
                    $customer->save();

                    // generate bill
                    CustomerBillGenerateController::generateBill($customer, $validity, PaymentPurpose::NEW_CUSTOMER->value);
                }
                break;
            case 'Hotspot':
                NewCustomersPaymentController::store($customer);
                break;
            case 'StaticIp':
            case 'Other':
                break;
        }

        //delete temp customer information
        $temp_customer->delete();

        // << custom_attributes
        if ($request->user()->role == 'manager') {
            $custom_fields = $request->user()->group_admin->custom_fields;
        } else {
            $custom_fields = $request->user()->custom_fields;
        }
        if ($custom_fields->count()) {
            foreach ($custom_fields as $custom_field) {
                if ($request->filled($custom_field->id)) {
                    $customer_custom_attribute = new customer_custom_attribute();
                    $customer_custom_attribute->customer_id = $customer->id;
                    $customer_custom_attribute->custom_field_id = $custom_field->id;
                    $customer_custom_attribute->value =  $request->input($custom_field->id);
                    $customer_custom_attribute->save();
                }
            }
        }
        // custom_attributes >>

        // show bill
        $customer->refresh();
        if ($customer->payment_status == 'billed') {
            return redirect()->route('customer_bills.index', ['customer_id' => $customer->id])->with('success', 'The customer has been added successfully!');
        }

        //return customer's list
        $url = route('customers.index') . '?refresh=1';
        return redirect($url)->with('success', 'The customer has been added successfully!');
    }
}
