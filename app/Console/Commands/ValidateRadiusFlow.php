<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Radcheck;
use App\Models\Radreply;
use Illuminate\Support\Facades\DB;

class ValidateRadiusFlow extends Command
{
    protected $signature = 'validate:radius-flow';
    protected $description = 'Validate basic RADIUS DB entries and connectivity';

    public function handle()
    {
        $this->info('Validating RADIUS tables presence...');

        try {
            $count = Radcheck::count();
            $this->info("radcheck rows: {$count}");
        } catch (\Throwable $e) {
            $this->error('Failed to access radcheck table: ' . $e->getMessage());
            return 1;
        }

        try {
            $count = Radreply::count();
            $this->info("radreply rows: {$count}");
        } catch (\Throwable $e) {
            $this->error('Failed to access radreply table: ' . $e->getMessage());
            return 1;
        }

        $this->info('RADIUS DB tables appear accessible.');

        // Basic sample check: ensure at least one user attribute exists for a user named 'test'
        $exists = Radcheck::where('username', 'test')->exists();
        $this->info('Sample user "test" exists in radcheck: ' . ($exists ? 'yes' : 'no'));

        return 0;
    }
}
