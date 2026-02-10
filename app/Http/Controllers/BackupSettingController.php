<?php

namespace App\Http\Controllers;

use App\Jobs\PrimaryAuthenticatorChangeJob;
use App\Models\backup_setting;
use App\Models\customer_backup_request;
use App\Models\Freeradius\nas;
use App\Models\operator;
use Illuminate\Http\Request;

class BackupSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $backup_settings = backup_setting::with('operator')->where('mgid', $operator->id)->get();

        $backup_requests = customer_backup_request::with(['backup_setting'])->where('mgid', $operator->id)->get();

        return view('admins.group_admin.backup-setting', [
            'backup_settings' => $backup_settings,
            'backup_requests' => $backup_requests,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $model = new nas();

        $model->setConnection($request->user()->radius_db_connection);

        $where = [
            ['mgid', '=', $request->user()->mgid],
        ];

        $routers = $model->where($where)->get();

        $operators = operator::where('mgid', $request->user()->id)->get();

        $operators = $operators->filter(function ($operator) {
            return $operator->role == 'group_admin' || $operator->role == 'operator' || $operator->role == 'sub_operator';
        });

        $backup_settings = backup_setting::where('mgid', $request->user()->id)->get();

        foreach ($backup_settings as $backup_setting) {
            $operators = $operators->except($backup_setting->operator_id);
        }

        return view('admins.group_admin.backup-setting-create', [
            'routers' => $routers,
            'operators' => $operators,
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
            'primary_authenticator' => 'required|in:Radius,Router',
            'nas_id' => 'required|numeric',
            'operator_id' => 'required|numeric',
            'backup_type' => 'required',
        ]);

        $count_where = [
            ['mgid', '=', $request->user()->id],
            ['operator_id', '=', $request->operator_id],
            ['nas_id', '=', $request->nas_id],
        ];

        $setting_count = backup_setting::where($count_where)->count();

        if ($setting_count) {
            return redirect()->route('backup_settings.index')->with('error', 'Duplicate Setting');
        }

        $model = new nas();
        $model->setConnection($request->user()->radius_db_connection);
        $nas = $model->find($request->nas_id);

        $backup_setting = new backup_setting();
        $backup_setting->mgid = $request->user()->id;
        $backup_setting->operator_id = $request->operator_id;
        $backup_setting->primary_authenticator = $request->primary_authenticator;
        $backup_setting->nas_id = $request->nas_id;
        $backup_setting->nas_ip = $nas->nasname;
        $backup_setting->backup_type = $request->backup_type;
        $backup_setting->save();

        PrimaryAuthenticatorChangeJob::dispatch($backup_setting)
            ->onConnection('database')
            ->onQueue('default');

        $resellers = operator::where('mgid', $request->user()->id)->get();

        $resellers = $resellers->filter(function ($operator) {
            return $operator->role == 'group_admin' || $operator->role == 'operator' || $operator->role == 'sub_operator';
        });

        foreach ($resellers as $reseller) {
            if (backup_setting::where('operator_id', $reseller->id)->count() == 0) {
                return redirect()->route('backup_settings.create')->with("success", "Settings saved successfully! Please Create Backup For Other Reseller");
            }
        }

        return redirect()->route('backup_settings.index')->with('success', 'Settings saved successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\backup_setting $backup_setting
     * @return \Illuminate\Http\Response
     */
    public function edit(backup_setting $backup_setting)
    {
        $admin = CacheController::getOperator($backup_setting->mgid);

        $original_router = CacheController::getNas($backup_setting->operator_id, $backup_setting->nas_id);
        if (!$original_router) {
            $backup_setting->delete();
            return redirect()->route('backup_settings.index');
        }

        $original_operator = CacheController::getOperator($backup_setting->operator_id);

        $model = new nas();

        $model->setConnection($admin->node_connection);

        $where = [
            ['mgid', '=', $admin->id],
        ];

        $routers = $model->where($where)->get()->except([$original_router->id]);

        $operators = operator::where('mgid', $admin->id)->get()->except([$original_operator->id]);

        $operators = $operators->filter(function ($operator) {
            return $operator->role == 'group_admin' || $operator->role == 'operator' || $operator->role == 'sub_operator';
        });

        return view('admins.group_admin.backup-setting-edit', [
            'backup_setting' => $backup_setting,
            'original_router' => $original_router,
            'original_operator' => $original_operator,
            'routers' => $routers,
            'operators' => $operators,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\backup_setting  $backup_setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, backup_setting $backup_setting)
    {
        $request->validate([
            'primary_authenticator' => 'required|in:Radius,Router',
            'nas_id' => 'required|numeric',
            'operator_id' => 'required|numeric',
            'backup_type' => 'required',
        ]);

        $model = new nas();
        $model->setConnection($request->user()->radius_db_connection);
        $nas = $model->find($request->nas_id);

        $backup_setting->operator_id = $request->operator_id;
        $backup_setting->nas_id = $nas->id;
        $backup_setting->nas_ip = $nas->nasname;
        $backup_setting->primary_authenticator = $request->primary_authenticator;
        $backup_setting->backup_type = $request->backup_type;
        $backup_setting->save();

        if ($backup_setting->wasChanged('primary_authenticator')) {
            PrimaryAuthenticatorChangeJob::dispatch($backup_setting)
                ->onConnection('database')
                ->onQueue('default');
        }

        return redirect()->route('backup_settings.index')->with('success', 'Settings saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\backup_setting  $backup_setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, backup_setting $backup_setting)
    {
        if ($request->user()->id !== $backup_setting->mgid) {
            abort(403);
        }

        $backup_setting->delete();

        return redirect()->route('backup_settings.index')->with('success', 'Setting deleted successfully');
    }
}
