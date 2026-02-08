<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Jobs\OperatorDeleteJob;
use App\Models\operator;
use App\Models\pgsql_activity_log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OperatorDestroyController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, operator $operator)
    {
        $group_admin = $request->user();

        $delete = 0;

        if ($operator->role == 'operator' && $operator->gid == $group_admin->id) {
            $delete = 1;
        }

        if ($delete == 0) {
            return redirect()->route('operators.index')->with('error', 'Not Authorized!');
        }

        $key = 'operator_destroy_' . $operator->id;
        $ttl = 1800;

        Cache::remember($key, $ttl, function () use ($group_admin, $operator) {

            $code = rand(100000, 999999);

            $group_admin->two_factor_recovery_codes = $code;
            $group_admin->save();

            $super_admin = operator::find($operator->sid);
            $message = SmsGenerator::OTP($super_admin, $code);

            $controller = new SmsGatewayController();
            try {
                $controller->sendSms($super_admin, $group_admin->mobile, $message);
            } catch (\Throwable $th) {
                //throw $th;
            }

            return $code;
        });

        return view('admins.group_admin.operator-destroy-create', [
            'code_receiver' => $group_admin->mobile,
            'operator' => $operator,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);

        $group_admin = $request->user();

        if ($group_admin->two_factor_recovery_codes !== $request->code) {
            return redirect()->route('operators.index')->with('error', 'Invalid Code!');
        }

        pgsql_activity_log::create([
            'gid' => $request->user()->gid,
            'operator_id' => $request->user()->id,
            'customer_id' => $operator->id,
            'topic' => 'destroy_operator',
            'year' => date(config('app.year_format')),
            'month' => date(config('app.month_format')),
            'week' => date(config('app.week_format')),
            'log' => $request->user()->name . ' has deleted operator: ' . $operator->name,
        ]);

        $operator->deleting = 1;
        $operator->save();

        OperatorDeleteJob::dispatch($operator)
            ->onConnection('database')
            ->onQueue('default');

        return redirect()->route('operators.index')->with('success', 'Job is processing');
    }
}
