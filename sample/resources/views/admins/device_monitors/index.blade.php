@section('contentTitle')
    <h3>Device Monitors</h3>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Device Monitoring Management</h3>
            <div class="card-tools">
                <a href="{{ route('device-monitors.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Add New Device
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Filters --}}
            <form method="GET" action="{{ route('device-monitors.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="device_type">Device Type</label>
                            <select name="device_type" id="device_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="ap" {{ request('device_type') == 'ap' ? 'selected' : '' }}>Access Point</option>
                                <option value="host" {{ request('device_type') == 'host' ? 'selected' : '' }}>Host Server</option>
                                <option value="mikrotik" {{ request('device_type') == 'mikrotik' ? 'selected' : '' }}>Mikrotik</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="up" {{ request('status') == 'up' ? 'selected' : '' }}>Online</option>
                                <option value="down" {{ request('status') == 'down' ? 'selected' : '' }}>Offline</option>
                                <option value="unknown" {{ request('status') == 'unknown' ? 'selected' : '' }}>Unknown</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="Device name or IP..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            {{-- Results count --}}
            <div class="mb-3">
                <strong>Total Devices:</strong> {{ $devices->total() }}
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Device Name</th>
                            <th>Type</th>
                            <th>IP Address</th>
                            <th>Monitor Method</th>
                            <th>Status</th>
                            <th>Response Time</th>
                            <th>Last Checked</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                            <tr>
                                <td>{{ $device->id }}</td>
                                <td>{{ $device->device_name }}</td>
                                <td>
                                    @if($device->device_type == 'ap')
                                        <span class="badge badge-primary"><i class="fas fa-wifi"></i> AP</span>
                                    @elseif($device->device_type == 'host')
                                        <span class="badge badge-warning"><i class="fas fa-server"></i> Host</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-network-wired"></i> Mikrotik</span>
                                    @endif
                                </td>
                                <td><code>{{ $device->ip_address }}</code></td>
                                <td>{{ strtoupper($device->monitor_method) }}</td>
                                <td>
                                    @if($device->status == 'up')
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Online</span>
                                    @elseif($device->status == 'down')
                                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Offline</span>
                                    @else
                                        <span class="badge badge-secondary"><i class="fas fa-question-circle"></i> Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $device->response_time ? $device->response_time . 'ms' : 'N/A' }}</td>
                                <td>{{ $device->last_checked_at ? $device->last_checked_at->format('Y-m-d H:i:s') : 'Never' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('device-monitors.show', $device) }}" class="btn btn-info btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('device-monitors.edit', $device) }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('device-monitors.destroy', $device) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this device?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No devices found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $devices->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

@section('pageJs')
@endsection
