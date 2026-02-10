<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeviceIdentificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function index(operator $operator)
    {
        return match ($operator->role) {
            'super_admin' => view('admins.super_admin.device-identification', ['operator' => $operator]),
            'group_admin' => view('admins.group_admin.device-identification', ['operator' => $operator]),
            'operator' => view('admins.operator.device-identification', ['operator' => $operator]),
            'sub_operator' => view('admins.sub_operator.device-identification', ['operator' => $operator]),
            'manager' => view('admins.manager.device-identification', ['operator' => $operator]),
            'sales_manager' => view('admins.sales_manager.device-identification', ['operator' => $operator]),
            'developer' => view('admins.developer.device-identification', ['operator' => $operator]),
        };
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(operator $operator)
    {

        if ($operator->mgid == config('consumer.demo_gid')) {
            return redirect()->route('operators.device-identification.index', ['operator' => $operator])->with('info', 'For Demo User this service is disabled');
        }

        $key = 'device_identification_' . $operator->id;
        $ttl = 1800;

        Cache::remember($key, $ttl, function () use ($operator) {
            $code = rand(100000, 999999);

            $operator->two_factor_recovery_codes = $code;
            $operator->save();
            $sms_operator =  match ($operator->role) {
                'developer' => operator::where('role', 'super_admin')->first(),
                'manager' => operator::where('id', $operator->gid)->first(),
                default => $operator,
            };

            $message = SmsGenerator::OTP($operator, $code);
            $controller = new SmsGatewayController();
            try {
                $controller->sendSms($sms_operator, $operator->mobile, $message);
            } catch (\Throwable $th) {
                //throw $th;
            }
            return $code;
        });

        return view('admins.components.operator-otp-verification', [
            'operator' => $operator,
            'action' => route('operators.device-identification.store', ['operator' => $operator]),
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

        if ($operator->two_factor_recovery_codes !== $request->code) {
            return redirect()->route('operators.device-identification.create', ['operator' => $operator])->with('info', 'Invalid Code!');
        }

        $operator->device_identification_enabled = 1;
        $operator->save();

        return redirect()->route('operators.device-identification.index', ['operator' => $operator]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $operator = $request->user();
        $operator->device_identification_enabled = 0;
        $operator->save();

        return redirect()->route('operators.device-identification.index', ['operator' => $operator])->with('info', 'The device identification service is disabled.');
    }
}
