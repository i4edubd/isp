{{-- Device Status Widget --}}
<div class="col-lg-3 col-md-4 col-sm-6 col-12">
    <div class="info-box bg-navy">
        <div class="info-box-content">
            <div class="inner">
                <h3 id="device_status_up">
                    <i class="fas fa-spinner fa-spin"></i>
                </h3>
                <p>Devices Online</p>
                <small class="text-muted">
                    <span id="device_status_down">0</span> Offline | 
                    <span id="device_status_total">0</span> Total
                </small>
            </div>
            <div class="progress mt-2">
                <div class="progress-bar bg-success" id="device_uptime_bar" style="width: 0%"></div>
            </div>
            <span class="progress-description">
                <span id="device_uptime_percentage">0</span>% Uptime
            </span>
        </div>
        <div class="icon">
            <i class="fas fa-network-wired"></i>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Function to update device status
    function updateDeviceStatus() {
        $.ajax({
            url: '{{ route("device_monitor.status") }}',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#device_status_up').html(data.up);
                $('#device_status_down').html(data.down);
                $('#device_status_total').html(data.total);
                $('#device_uptime_percentage').html(data.uptime_percentage);
                $('#device_uptime_bar').css('width', data.uptime_percentage + '%');
            },
            error: function() {
                $('#device_status_up').html('<i class="fas fa-exclamation-triangle"></i>');
            }
        });
    }

    // Update immediately on page load
    $(document).ready(function() {
        updateDeviceStatus();
        
        // Auto-refresh interval (in milliseconds) - configurable
        var refreshInterval = {{ config('ispbills.device_status_refresh_interval', 10000) }};
        
        // Auto-refresh at specified interval
        setInterval(updateDeviceStatus, refreshInterval);
    });
</script>
@endpush
