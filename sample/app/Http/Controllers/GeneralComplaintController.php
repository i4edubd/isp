<?php

namespace App\Http\Controllers;

use App\Models\complain_category;
use App\Models\department;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class GeneralComplaintController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        $departments = department::where('operator_id', $operator->id)->get();

        $complain_categories = complain_category::where('operator_id', $operator->id)->get();

        return view('complaint_management.general-complaint-create', [
            'departments' => $departments,
            'complain_categories' => $complain_categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer = customer::make([
            "id" => 0,
            "mobile" => "",
            "username" => "",
        ]);

        $controller = new CustomerComplainController();

        return $controller->store($request, $customer);
    }
}
