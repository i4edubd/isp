<?php

namespace App\Http\Controllers;

use App\Jobs\SmsBroadcastJob;
use App\Models\Freeradius\customer;
use App\Models\sms_broadcast_job;
use Illuminate\Http\Request;

class SmsBroadcastJobController extends Controller
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
                return view('admins.group_admin.sms-broadcast');
                break;

            case 'operator':
                return view('admins.operator.sms-broadcast');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-broadcast');
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
            'connection_type' => 'required|in:PPPoE,Hotspot,StaticIp',
            'status' => 'required|in:active,suspended,disabled',
            'payment_status' => 'nullable|in:billed,paid',
            'zone_id' => 'nullable|numeric',
            'message' => 'required|string',
        ]);

        // << filter
        $filter = [];

        $filter[] = ['operator_id', '=', $request->user()->id];

        $filter[] = ['connection_type', '=', $request->connection_type];

        $filter[] = ['status', '=', $request->status];

        if ($request->filled('payment_status')) {
            $filter[] = ['payment_status', '=', $request->payment_status];
        }

        if ($request->filled('zone_id')) {
            $filter[] = ['zone_id', '=', $request->zone_id];
        }

        $json_filter = json_encode($filter);
        // filter >>

        $sms_broadcast_job = new sms_broadcast_job();
        $sms_broadcast_job->operator_id = $request->user()->id;
        $sms_broadcast_job->filter = $json_filter;
        $sms_broadcast_job->message = $request->message;
        $sms_broadcast_job->message_count = customer::where($filter)->count();
        $sms_broadcast_job->save();

        return redirect()->route('sms-broadcast-jobs.edit', ['sms_broadcast_job' => $sms_broadcast_job->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\sms_broadcast_job  $sms_broadcast_job
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, sms_broadcast_job $sms_broadcast_job)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.sms-broadcast-edit', [
                    'sms_broadcast_job' => $sms_broadcast_job,
                ]);
                break;

            case 'operator':
                return view('admins.group_admin.sms-broadcast-edit', [
                    'sms_broadcast_job' => $sms_broadcast_job,
                ]);
                break;

            case 'sub_operator':
                return view('admins.group_admin.sms-broadcast-edit', [
                    'sms_broadcast_job' => $sms_broadcast_job,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\sms_broadcast_job  $sms_broadcast_job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, sms_broadcast_job $sms_broadcast_job)
    {
        SmsBroadcastJob::dispatch($sms_broadcast_job)
            ->onConnection('database')
            ->onQueue('default');

        return redirect()->route('sms_histories.index')->with('success', 'Job is running');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\sms_broadcast_job  $sms_broadcast_job
     * @return \Illuminate\Http\Response
     */
    public function destroy(sms_broadcast_job $sms_broadcast_job)
    {
        $sms_broadcast_job->delete();

        return redirect()->route('sms-broadcast-jobs.create')->with('error', 'Job is Cancelled');
    }
}
