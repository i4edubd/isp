<?php

namespace App\Http\Controllers;

use App\Models\device_monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DeviceMonitorController extends Controller
{
    /**
     * Roles allowed to access Device Monitoring
     */
    private const ALLOWED_ROLES = ['super_admin', 'developer', 'group_admin'];

    /**
     * Empty device status response for unauthorized users
     */
    private const EMPTY_STATUS_RESPONSE = [
        'total' => 0,
        'up' => 0,
        'down' => 0,
        'unknown' => 0,
        'uptime_percentage' => 0,
        'by_type' => [
            'ap' => ['total' => 0, 'up' => 0, 'down' => 0],
            'host' => ['total' => 0, 'up' => 0, 'down' => 0],
            'mikrotik' => ['total' => 0, 'up' => 0, 'down' => 0],
        ],
    ];

    /**
     * Check if the current user has permission to access Device Monitoring
     *
     * @param  \App\Models\operator  $user
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function checkAccess($user)
    {
        if (!in_array($user->role, self::ALLOWED_ROLES)) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Display a listing of device monitors
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Build the query based on user role
        $query = device_monitor::query();

        // Apply role-based filtering
        switch ($user->role) {
            case 'super_admin':
            case 'developer':
                // Can see all devices
                break;
            
            case 'group_admin':
                // Can see devices from their group
                $query->where('gid', $user->id);
                break;
            
            default:
                // Operators and suboperators cannot access Device Monitoring
                abort(403, 'Unauthorized action.');
                break;
        }

        // Apply filters
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('device_name', 'like', '%' . $request->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $request->search . '%');
            });
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $perPage = $request->get('per_page', 30);
        $devices = $query->paginate($perPage);

        // Determine view based on role
        if ($user->role === 'group_admin') {
            return view('admins.group_admin.device_monitors_index', compact('devices'));
        }

        return view('admins.super_admin.device_monitors_index', compact('devices'));
    }

    /**
     * Show the form for creating a new device monitor
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = $request->user();
        
        $this->checkAccess($user);
        
        if ($user->role === 'group_admin') {
            return view('admins.group_admin.device_monitors_create');
        }

        return view('admins.super_admin.device_monitors_create');
    }

    /**
     * Store a newly created device monitor
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        $this->checkAccess($user);
        
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|in:ap,host,mikrotik',
            'ip_address' => 'required|ip',
            'monitor_method' => 'required|in:ping,snmp',
            'port' => 'nullable|integer|min:1|max:65535',
            'snmp_community' => 'nullable|string|max:64',
        ]);
        
        // Determine gid: all authorized users use their own ID
        $gid = $user->id;

        device_monitor::create([
            'operator_id' => $user->id,
            'gid' => $gid,
            'device_name' => $validated['device_name'],
            'device_type' => $validated['device_type'],
            'ip_address' => $validated['ip_address'],
            'monitor_method' => $validated['monitor_method'],
            'port' => $validated['port'] ?? null,
            'snmp_community' => $validated['snmp_community'] ?? null,
            'status' => 'unknown',
        ]);

        return redirect()->route('device-monitors.index')
            ->with('success', 'Device monitor created successfully.');
    }

    /**
     * Display the specified device monitor
     *
     * @param  \App\Models\device_monitor  $deviceMonitor
     * @return \Illuminate\Http\Response
     */
    public function show(device_monitor $deviceMonitor)
    {
        $user = auth()->user();
        
        $this->checkAccess($user);
        
        $this->authorize('view', $deviceMonitor);
        
        if ($user->role === 'group_admin') {
            return view('admins.group_admin.device_monitors_show', compact('deviceMonitor'));
        }

        return view('admins.super_admin.device_monitors_show', compact('deviceMonitor'));
    }

    /**
     * Show the form for editing the specified device monitor
     *
     * @param  \App\Models\device_monitor  $deviceMonitor
     * @return \Illuminate\Http\Response
     */
    public function edit(device_monitor $deviceMonitor)
    {
        $user = auth()->user();
        
        $this->checkAccess($user);
        
        $this->authorize('update', $deviceMonitor);
        
        if ($user->role === 'group_admin') {
            return view('admins.group_admin.device_monitors_edit', compact('deviceMonitor'));
        }

        return view('admins.super_admin.device_monitors_edit', compact('deviceMonitor'));
    }

    /**
     * Update the specified device monitor
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\device_monitor  $deviceMonitor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, device_monitor $deviceMonitor)
    {
        $user = $request->user();
        
        $this->checkAccess($user);
        
        $this->authorize('update', $deviceMonitor);

        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|in:ap,host,mikrotik',
            'ip_address' => 'required|ip',
            'monitor_method' => 'required|in:ping,snmp',
            'port' => 'nullable|integer|min:1|max:65535',
            'snmp_community' => 'nullable|string|max:64',
        ]);

        $deviceMonitor->update($validated);

        return redirect()->route('device-monitors.index')
            ->with('success', 'Device monitor updated successfully.');
    }

    /**
     * Remove the specified device monitor
     *
     * @param  \App\Models\device_monitor  $deviceMonitor
     * @return \Illuminate\Http\Response
     */
    public function destroy(device_monitor $deviceMonitor)
    {
        $user = auth()->user();
        
        $this->checkAccess($user);
        
        $this->authorize('delete', $deviceMonitor);

        $deviceMonitor->delete();

        return redirect()->route('device-monitors.index')
            ->with('success', 'Device monitor deleted successfully.');
    }

    /**
     * Get device status counts for dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeviceStatus(Request $request)
    {
        $user = $request->user();
        
        $this->checkAccess($user);
        
        // Cache key based on user
        $cacheKey = 'device_status_' . $user->id;

        // Cache for 10 seconds to match the AJAX refresh rate
        $data = Cache::remember($cacheKey, 10, function () use ($user) {
            $query = device_monitor::query();

            // Apply role-based filtering
            switch ($user->role) {
                case 'super_admin':
                case 'developer':
                    // Can see all devices
                    break;
                
                case 'group_admin':
                    // Can see devices from their group
                    $query->where('gid', $user->id);
                    break;
                
                default:
                    // Operators and suboperators cannot access Device Monitoring
                    // Return empty data
                    return self::EMPTY_STATUS_RESPONSE;
            }

            $totalDevices = $query->count();
            $upDevices = (clone $query)->where('status', 'up')->count();
            $downDevices = (clone $query)->where('status', 'down')->count();
            $unknownDevices = (clone $query)->where('status', 'unknown')->count();

            // Get device counts by type
            $apCount = (clone $query)->where('device_type', 'ap')->count();
            $hostCount = (clone $query)->where('device_type', 'host')->count();
            $mikrotikCount = (clone $query)->where('device_type', 'mikrotik')->count();

            // Get up/down counts by type
            $apUp = (clone $query)->where('device_type', 'ap')->where('status', 'up')->count();
            $apDown = (clone $query)->where('device_type', 'ap')->where('status', 'down')->count();
            $hostUp = (clone $query)->where('device_type', 'host')->where('status', 'up')->count();
            $hostDown = (clone $query)->where('device_type', 'host')->where('status', 'down')->count();
            $mikrotikUp = (clone $query)->where('device_type', 'mikrotik')->where('status', 'up')->count();
            $mikrotikDown = (clone $query)->where('device_type', 'mikrotik')->where('status', 'down')->count();

            return [
                'total' => $totalDevices,
                'up' => $upDevices,
                'down' => $downDevices,
                'unknown' => $unknownDevices,
                'uptime_percentage' => $totalDevices > 0 ? round(($upDevices / $totalDevices) * 100, 1) : 0,
                'by_type' => [
                    'ap' => ['total' => $apCount, 'up' => $apUp, 'down' => $apDown],
                    'host' => ['total' => $hostCount, 'up' => $hostUp, 'down' => $hostDown],
                    'mikrotik' => ['total' => $mikrotikCount, 'up' => $mikrotikUp, 'down' => $mikrotikDown],
                ],
            ];
        });

        return response()->json($data);
    }
}
