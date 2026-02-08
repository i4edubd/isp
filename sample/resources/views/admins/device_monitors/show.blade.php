@section('contentTitle')
    <h3>Device Details</h3>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Device Monitor Details</h3>
            <div class="card-tools">
                <a href="{{ route('device-monitors.edit', $deviceMonitor) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('device-monitors.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Device Name</th>
                            <td>{{ $deviceMonitor->device_name }}</td>
                        </tr>
                        <tr>
                            <th>Device Type</th>
                            <td>
                                @if($deviceMonitor->device_type == 'ap')
                                    <span class="badge badge-primary"><i class="fas fa-wifi"></i> Access Point</span>
                                @elseif($deviceMonitor->device_type == 'host')
                                    <span class="badge badge-warning"><i class="fas fa-server"></i> Host Server</span>
                                @else
                                    <span class="badge badge-danger"><i class="fas fa-network-wired"></i> Mikrotik</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td><code>{{ $deviceMonitor->ip_address }}</code></td>
                        </tr>
                        <tr>
                            <th>Monitor Method</th>
                            <td>{{ strtoupper($deviceMonitor->monitor_method) }}</td>
                        </tr>
                        @if($deviceMonitor->monitor_method == 'snmp')
                        <tr>
                            <th>SNMP Port</th>
                            <td>{{ $deviceMonitor->port ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>SNMP Community</th>
                            <td>{{ $deviceMonitor->snmp_community ?? 'N/A' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Status</th>
                            <td>
                                @if($deviceMonitor->status == 'up')
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Online</span>
                                @elseif($deviceMonitor->status == 'down')
                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Offline</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-question-circle"></i> Unknown</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Response Time</th>
                            <td>{{ $deviceMonitor->response_time ? $deviceMonitor->response_time . ' ms' : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Last Checked</th>
                            <td>{{ $deviceMonitor->last_checked_at ? $deviceMonitor->last_checked_at->format('Y-m-d H:i:s') : 'Never' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $deviceMonitor->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $deviceMonitor->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @if($deviceMonitor->last_error)
                        <tr>
                            <th>Last Error</th>
                            <td class="text-danger">{{ $deviceMonitor->last_error }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <form action="{{ route('device-monitors.destroy', $deviceMonitor) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this device monitor?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Device
                </button>
            </form>
        </div>
    </div>
@endsection

@section('pageJs')
@endsection
