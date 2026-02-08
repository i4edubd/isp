<?php

namespace App\Http\Controllers;

use App\Models\activity_log;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Build the query based on user role
        $query = activity_log::query();

        // Apply role-based filtering
        switch ($user->role) {
            case 'super_admin':
            case 'developer':
                // Can see all activity logs
                break;
            
            case 'group_admin':
                // Can see logs from their group
                $query->where('gid', $user->id);
                break;
            
            case 'operator':
            case 'sub_operator':
            case 'manager':
                // Can see only their own logs
                $query->where('operator_id', $user->id);
                break;
            
            default:
                // Default: only own logs
                $query->where('operator_id', $user->id);
                break;
        }

        // Apply filters
        if ($request->filled('topic')) {
            $query->where('topic', $request->topic);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }

        if ($request->filled('search')) {
            $query->where('log', 'like', '%' . $request->search . '%');
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $perPage = $request->get('per_page', 30);
        $activityLogs = $query->with(['operator', 'customer'])->paginate($perPage);

        // Get unique topics for filter dropdown
        $topics = activity_log::select('topic')
            ->distinct()
            ->whereNotNull('topic')
            ->pluck('topic');

        return view('admins.activity_logs.index', compact('activityLogs', 'topics'));
    }
}
