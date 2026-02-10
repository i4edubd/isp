<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\operator;
use App\Models\sms_history;
use Illuminate\Http\Request;

class SmsHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $where = [];

        // default filter
        $where[] = ['operator_id', '=', $operator->id];

        // sms_bill_id
        if ($request->filled('sms_bill_id')) {
            $where[] = ['sms_bill_id', '=', $request->sms_bill_id];
        }

        // to_number
        if ($request->filled('to_number')) {
            $where[] = ['to_number', '=', $request->to_number];
        }

        $histories = sms_history::where($where)->orderBy('id', 'desc')->paginate(15);

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.sms-histories', [
                    'histories' => $histories,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.sms-histories', [
                    'histories' => $histories,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-histories', [
                    'histories' => $histories,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-histories', [
                    'histories' => $histories,
                ]);
                break;

            case 'sales_manager':
                return view('admins.sales_manager.sms-histories', [
                    'histories' => $histories,
                ]);
                break;

            case 'developer':
                return view('admins.developer.sms-histories', [
                    'histories' => $histories,
                ]);
                break;
        }
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

            case 'super_admin':
                return view('admins.super_admin.sms-send');
                break;

            case 'group_admin':
                return view('admins.group_admin.sms-send');
                break;

            case 'operator':
                return view('admins.operator.sms-send');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-send');
                break;

            case 'sales_manager':
                return view('admins.sales_manager.sms-send');
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
            'mobile' => 'required',
            'message' => 'required',
        ]);

        $mobile = validate_mobile($request->mobile);

        if ($mobile == 0) {
            abort(500, 'Invalid Mobile Number!');
        }

        $controller = new SmsGatewayController();
        $sms_history = $controller->sendSms($request->user(), $mobile, $request->message);
        return redirect()->route('sms_histories.show', ['sms_history' => $sms_history]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\sms_history  $sms_history
     * @return \Illuminate\Http\Response
     */
    public function show(sms_history $sms_history)
    {
        $operator = operator::find($sms_history->operator_id);

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.sms-status', [
                    'sms_history' => $sms_history,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.sms-status', [
                    'sms_history' => $sms_history,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-status', [
                    'sms_history' => $sms_history,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-status', [
                    'sms_history' => $sms_history,
                ]);
                break;

            case 'sales_manager':
                return view('admins.sales_manager.sms-status', [
                    'sms_history' => $sms_history,
                ]);
                break;
        }
    }
}
