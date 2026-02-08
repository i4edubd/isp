<?php

namespace App\Http\Controllers;

use App\Models\pgsql_activity_log;
use Illuminate\Http\Request;

class PgsqlActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                $activity_logs = pgsql_activity_log::where('gid', $operator->id)->get();
                break;

            case 'operator':
                $activity_logs = pgsql_activity_log::where('gid', $operator->id)
                    ->orWhere('operator_id', $operator->id)->get();
                break;

            case 'sub_operator':
                $activity_logs = pgsql_activity_log::where('gid', $operator->id)
                    ->orWhere('operator_id', $operator->id)->get();
                break;

            case 'manager':
                $activity_logs = pgsql_activity_log::where('gid', $operator->gid)
                    ->orWhere('operator_id', $operator->id)->get();
                break;

            default:
                $activity_logs = pgsql_activity_log::where('operator_id', $operator->id)->get();
                break;
        }

        if ($request->filled('operator_id')) {
            $operator_id = $request->operator_id;
            $activity_logs = $activity_logs->filter(function ($activity_log) use ($operator_id) {
                return $activity_log->operator_id == $operator_id;
            });
        }

        if ($request->filled('topic')) {
            $topic = $request->topic;
            $activity_logs = $activity_logs->filter(function ($activity_log) use ($topic) {
                return $activity_log->topic == $topic;
            });
        }

        if ($request->filled('year')) {
            $year = $request->year;
            $activity_logs = $activity_logs->filter(function ($activity_log) use ($year) {
                return $activity_log->year == $year;
            });
        }

        if ($request->filled('month')) {
            $month = $request->month;
            $activity_logs = $activity_logs->filter(function ($activity_log) use ($month) {
                return $activity_log->month == $month;
            });
        }

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.activity_logs', [
                    'activity_logs' => $activity_logs,
                ]);
                break;

            case 'operator':
                return view('admins.operator.activity_logs', [
                    'activity_logs' => $activity_logs,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.activity_logs', [
                    'activity_logs' => $activity_logs,
                ]);
                break;

            case 'manager':
                return view('admins.manager.activity_logs', [
                    'activity_logs' => $activity_logs,
                ]);
                break;
        }
    }
}
