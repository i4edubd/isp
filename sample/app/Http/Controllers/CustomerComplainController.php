<?php

namespace App\Http\Controllers;

use App\Models\complain_category;
use App\Models\complain_comment;
use App\Models\complain_ledger;
use App\Models\customer_complain;
use App\Models\department;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;

class CustomerComplainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()) {
            $requester = $request->user();
        } else {
            abort(403);
        }

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        $filter = [];

        // default
        $filter[] = ['operator_id', '=', $operator->id];
        $filter[] = ['is_active', '=', 1];

        // department_id
        if ($request->filled('department_id')) {
            $filter[] = ['department_id', '=', $request->department_id];
        }

        // category_id
        if ($request->filled('category_id')) {
            $filter[] = ['category_id', '=', $request->category_id];
        }

        // default length
        $length = 10;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $departments = department::where('operator_id', $operator->id)->get();

        $complain_categories = complain_category::where('operator_id', $operator->id)->get();

        $complaints = customer_complain::where($filter)->paginate($length);

        return view('complaint_management.complaints', [
            'departments' => $departments,
            'complain_categories' => $complain_categories,
            'complaints' => $complaints,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        $departments = department::where('operator_id', $operator->id)->get();

        $complain_categories = complain_category::where('operator_id', $operator->id)->get();

        return view('complaint_management.customer_complain-create', [
            'departments' => $departments,
            'complain_categories' => $complain_categories,
            'customer' => $customer,
        ]);
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
        if ($request->user()) {
            $requester = $request->user();
            if ($requester->role == 'manager') {
                $operator = $requester->group_admin;
            } else {
                $operator = $requester;
            }
        } else {
            $requester = $customer;
            $operator = operator::findOrFail($customer->operator_id);
        }

        $request->validate([
            'category_id' => 'required',
            'message' => 'required',
        ]);

        $customer_complain = new customer_complain();
        $customer_complain->operator_id = $operator->id;
        $customer_complain->customer_id = $customer->id;
        $customer_complain->mobile = $customer->mobile;
        $customer_complain->username = $customer->username;
        $customer_complain->category_id = $request->category_id;
        $customer_complain->department_id = $request->department_id;
        if ($requester->role == 'customer') {
            $customer_complain->requester = "customer";
            $customer_complain->ack_status = 0;
            $customer_complain->ack_by = 0;
            $customer_complain->receiver_id = 0;
        } else {
            $customer_complain->ack_status = 1;
            $customer_complain->ack_by = $requester->id;
            $customer_complain->receiver_id = $requester->id;
        }
        $customer_complain->message = $request->message;
        $customer_complain->start_date = date(config('app.date_format'));
        $customer_complain->start_time = date(config('app.date_time_format'));
        $customer_complain->week = date(config('app.week_format'));
        $customer_complain->month = date(config('app.month_format'));
        $customer_complain->year = date(config('app.year_format'));
        $customer_complain->save();

        return redirect()->route('customer_complains.index')->with('success', 'Complaint has been added successfully!');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\customer_complain  $customer_complain
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, customer_complain $customer_complain)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        $customer = customer::where('id', $customer_complain->customer_id)->firstOr(function () {
            return customer::make([
                "name" => "",
                "mobile" => "",
                "username" => "",
            ]);
        });

        $departments = department::where('operator_id', $operator->id)->get()->except($customer_complain->department_id);

        $complain_categories = complain_category::where('operator_id', $operator->id)->get()->except($customer_complain->category_id);

        $complain_ledgers = complain_ledger::where('complain_id', $customer_complain->id)->get();

        $complain_comments = complain_comment::where('complain_id', $customer_complain->id)->get();

        return view('complaint_management.complaint-details', [
            'customer_complain' => $customer_complain,
            'departments' => $departments,
            'complain_categories' => $complain_categories,
            'complain_ledgers' => $complain_ledgers,
            'complain_comments' => $complain_comments,
            'customer' => $customer,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\customer_complain  $customer_complain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, customer_complain $customer_complain)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        if ($operator->id !== $customer_complain->operator_id) {
            abort(403);
        }

        $customer_complain->delete();

        return redirect()->route('customer_complains.index')->with('success', 'Complain Deleted successfully!');
    }
}
