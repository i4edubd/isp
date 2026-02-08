@section('contentTitle')
    <h3>Add New Device</h3>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Add Device Monitor</h3>
        </div>
        <form action="{{ route('device-monitors.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="device_name">Device Name <span class="text-danger">*</span></label>
                            <input type="text" name="device_name" id="device_name" class="form-control" 
                                   value="{{ old('device_name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="device_type">Device Type <span class="text-danger">*</span></label>
                            <select name="device_type" id="device_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="ap" {{ old('device_type') == 'ap' ? 'selected' : '' }}>Access Point</option>
                                <option value="host" {{ old('device_type') == 'host' ? 'selected' : '' }}>Host Server</option>
                                <option value="mikrotik" {{ old('device_type') == 'mikrotik' ? 'selected' : '' }}>Mikrotik</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ip_address">IP Address <span class="text-danger">*</span></label>
                            <input type="text" name="ip_address" id="ip_address" class="form-control" 
                                   value="{{ old('ip_address') }}" placeholder="192.168.1.1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="monitor_method">Monitor Method <span class="text-danger">*</span></label>
                            <select name="monitor_method" id="monitor_method" class="form-control" required>
                                <option value="ping" {{ old('monitor_method') == 'ping' ? 'selected' : '' }}>Ping</option>
                                <option value="snmp" {{ old('monitor_method') == 'snmp' ? 'selected' : '' }}>SNMP</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" id="snmp_fields" style="display: none;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="port">SNMP Port</label>
                            <input type="number" name="port" id="port" class="form-control" 
                                   value="{{ old('port', 161) }}" min="1" max="65535">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="snmp_community">SNMP Community</label>
                            <input type="text" name="snmp_community" id="snmp_community" class="form-control" 
                                   value="{{ old('snmp_community', 'public') }}" placeholder="public">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Device
                </button>
                <a href="{{ route('device-monitors.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@section('pageJs')
<script>
    $(document).ready(function() {
        function toggleSNMPFields() {
            if ($('#monitor_method').val() === 'snmp') {
                $('#snmp_fields').show();
            } else {
                $('#snmp_fields').hide();
            }
        }

        toggleSNMPFields();
        $('#monitor_method').change(toggleSNMPFields);
    });
</script>
@endsection
