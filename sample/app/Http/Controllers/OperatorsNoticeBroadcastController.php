<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\operator;
use Illuminate\Http\Request;

class OperatorsNoticeBroadcastController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $where = [
            ['role', '=', 'operator'],
            ['gid', '=', $request->user()->id],
        ];

        $operators_count = operator::where($where)->count();

        return view('admins.group_admin.notice-broadcast', [
            'operators_count' => $operators_count,
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
            'text_message' => 'required|string',
        ]);

        $where = [
            ['role', '=', 'operator'],
            ['gid', '=', $request->user()->id],
        ];

        $controller = new SmsGatewayController();

        $operators = operator::where($where)->get();
        foreach ($operators as $operator) {
            $controller->sendSms($request->user(), $operator->mobile, $request->text_message, 0);
        }

        return redirect()->route('sms_histories.index')->with('success', 'Notice Sent Successfully!');
    }
}
