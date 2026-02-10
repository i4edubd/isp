<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\operator;
use Illuminate\Http\Request;

class DeveloperNoticeBroadcastController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $where = [
            ['role', '=', 'group_admin'],
        ];

        $operators_count = operator::where($where)->count();

        return view('admins.developer.notice-broadcast', [
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
            ['role', '=', 'group_admin'],
        ];

        $controller = new SmsGatewayController();

        $operators = operator::where($where)->get();

        foreach ($operators as $operator) {
            $mobile = validate_mobile($operator->mobile);
            if ($mobile == 0) {
                continue;
            }
            $controller->sendSms($request->user(), $operator->mobile, $request->text_message, 0);
        }

        return redirect()->route('sms_histories.index')->with('success', 'Notice Sent Successfully!');
    }
}
