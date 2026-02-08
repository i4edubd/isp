<?php

namespace App\Console\Commands;

use App\Models\device_monitor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:monitor {--type=all : Monitor specific device type (ap, host, mikrotik, or all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor devices (APs, hosts, Mikrotik) using Netwatch (ping/SNMP)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->option('type');
        $this->info('Starting device monitoring...');

        $query = device_monitor::query();
        
        if ($type !== 'all') {
            $query->where('device_type', $type);
        }

        $devices = $query->get();
        $total = $devices->count();
        $checked = 0;

        $this->info("Found {$total} devices to monitor.");

        foreach ($devices as $device) {
            $this->monitorDevice($device);
            $checked++;
            
            if ($checked % 10 === 0) {
                $this->info("Checked {$checked}/{$total} devices...");
            }
        }

        $this->info("Device monitoring completed. Checked {$checked} devices.");
        
        return 0;
    }

    /**
     * Monitor a single device
     *
     * @param device_monitor $device
     * @return void
     */
    protected function monitorDevice($device)
    {
        try {
            $startTime = microtime(true);

            if ($device->monitor_method === 'ping') {
                $status = $this->pingDevice($device->ip_address);
            } else {
                $status = $this->snmpCheck($device);
            }

            $responseTime = round((microtime(true) - $startTime) * 1000);

            $device->update([
                'status' => $status ? 'up' : 'down',
                'last_checked_at' => now(),
                'response_time' => $responseTime,
                'last_error' => $status ? null : 'Device unreachable',
            ]);
        } catch (\Exception $e) {
            $device->update([
                'status' => 'down',
                'last_checked_at' => now(),
                'last_error' => $e->getMessage(),
            ]);
            Log::error("Error monitoring device {$device->device_name}: " . $e->getMessage());
        }
    }

    /**
     * Ping a device
     *
     * @param string $ipAddress
     * @return bool
     */
    protected function pingDevice($ipAddress)
    {
        // Validate IP address to prevent command injection
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            Log::warning("Invalid IP address for ping: {$ipAddress}");
            return false;
        }

        // Use system ping command with validated IP
        $command = sprintf(
            'ping -c 1 -W 2 %s > /dev/null 2>&1',
            escapeshellarg($ipAddress)
        );

        exec($command, $output, $returnVar);

        return $returnVar === 0;
    }

    /**
     * Check device via SNMP
     *
     * @param device_monitor $device
     * @return bool
     */
    protected function snmpCheck($device)
    {
        // Check if SNMP extension is available
        if (!function_exists('snmpget')) {
            Log::debug('SNMP extension not available, falling back to ping');
            return $this->pingDevice($device->ip_address);
        }

        // Validate IP address
        if (!filter_var($device->ip_address, FILTER_VALIDATE_IP)) {
            Log::warning("Invalid IP address for SNMP: {$device->ip_address}");
            return false;
        }

        try {
            // Disable error reporting temporarily for SNMP
            $oldErrorReporting = error_reporting(0);
            
            $result = snmpget(
                $device->ip_address,
                $device->snmp_community ?: 'public',
                'sysUpTime.0',
                1000000,
                1
            );
            
            // Restore error reporting
            error_reporting($oldErrorReporting);

            return $result !== false;
        } catch (\Exception $e) {
            Log::warning("SNMP check failed for {$device->device_name}: " . $e->getMessage());
            return false;
        }
    }
}
