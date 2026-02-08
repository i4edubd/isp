<?php

namespace Database\Seeders;

use App\Models\device_monitor;
use App\Models\operator;
use Illuminate\Database\Seeder;

class SampleDeviceMonitorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get first operator for demo purposes
        $operator = operator::first();

        if (!$operator) {
            $this->command->error('No operators found. Please create an operator first.');
            return;
        }

        $sampleDevices = [
            [
                'device_type' => 'ap',
                'device_name' => 'Main Office Access Point',
                'ip_address' => '192.168.1.10',
                'monitor_method' => 'ping',
            ],
            [
                'device_type' => 'ap',
                'device_name' => 'Branch Office AP',
                'ip_address' => '192.168.1.11',
                'monitor_method' => 'ping',
            ],
            [
                'device_type' => 'host',
                'device_name' => 'Gateway Server',
                'ip_address' => '192.168.1.1',
                'monitor_method' => 'ping',
            ],
            [
                'device_type' => 'host',
                'device_name' => 'DNS Server',
                'ip_address' => '192.168.1.2',
                'monitor_method' => 'ping',
            ],
            [
                'device_type' => 'mikrotik',
                'device_name' => 'Core Router MikroTik',
                'ip_address' => '192.168.1.254',
                'monitor_method' => 'snmp',
                'snmp_community' => 'public',
                'port' => 161,
            ],
            [
                'device_type' => 'mikrotik',
                'device_name' => 'Distribution Router',
                'ip_address' => '192.168.2.1',
                'monitor_method' => 'ping',
            ],
        ];

        foreach ($sampleDevices as $device) {
            // Determine gid - for group admins, use their own id; for operators, use their gid
            $gid = $operator->role === 'group_admin' ? $operator->id : ($operator->gid ?? $operator->id);
            
            device_monitor::create([
                'operator_id' => $operator->id,
                'gid' => $gid,
                'device_type' => $device['device_type'],
                'device_name' => $device['device_name'],
                'ip_address' => $device['ip_address'],
                'monitor_method' => $device['monitor_method'],
                'port' => $device['port'] ?? null,
                'snmp_community' => $device['snmp_community'] ?? null,
                'status' => 'unknown',
            ]);
        }

        $this->command->info('Sample device monitors seeded successfully.');
        $this->command->info('Run "php artisan devices:monitor" to check device statuses.');
    }
}
