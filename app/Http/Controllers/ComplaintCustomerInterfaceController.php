<?php

namespace App\Http\Controllers;

use App\Models\complain_category;
use App\Models\complain_comment;
use App\Models\complain_ledger;
use App\Models\customer_complain;
use Illuminate\Http\Request;

class ComplaintCustomerInterfaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);

        $complaints_where = [
            ['operator_id', '=', $operator->id],
            ['customer_id', '=', $all_customer->customer_id],
        ];
        $complaints = customer_complain::where($complaints_where)->get();

        return view('customers.complaints', [
            'operator' => $operator,
            'complaints' => $complaints,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);
        $complain_categories = complain_category::where('operator_id', $operator->id)->get();

        return view('customers.complaint-create', [
            'complain_categories' => $complain_categories,
            'operator' => $operator,
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
        $request->validate([
            'category_id' => 'required',
            'message' => 'required',
        ]);

        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);

        $controller = new CustomerComplainController();
        $controller->store($request, $customer);

        return redirect()->route('complaints-customer-interface.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_complain  $customer_complain
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, customer_complain $customer_complain)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($all_customer->operator_id);
        $complain_comments = complain_comment::where('complain_id', $customer_complain->id)->get();
        $complain_ledgers = complain_ledger::where('complain_id', $customer_complain->id)->get();

        return view('customers.complaints-details', [
            'customer' => $customer,
            'operator' => $operator,
            'customer_complain' => $customer_complain,
            'complain_comments' => $complain_comments,
            'complain_ledgers' => $complain_ledgers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_complain  $customer_complain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer_complain $customer_complain)
    {

        $controller = new ComplainCommentController();

        $controller->store($request, $customer_complain);

        return redirect()->route('complaints-customer-interface.show', ['customer_complain' => $customer_complain->id]);
    }
}
