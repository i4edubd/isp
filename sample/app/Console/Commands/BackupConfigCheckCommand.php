<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupConfigCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:backup_config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup Config check command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (collect(config('backup.backup.destination.disks'))->first(function ($value, $key) {
            return $value == 'ftp' || $value == 'local';
        })) {
            $this->info('Found Backup Disk!');
            $disk = collect(config('backup.backup.destination.disks'))->first(function ($value, $key) {
                return $value == 'ftp' || $value == 'local';
            });
            $this->info($disk);
            if ($disk == 'local') {
                $this->error('Local Backup!');
            }
            print_r(config('filesystems.disks.ftp'));
        } else {
            $this->error('Backup Disk Not Found! ' . config('app.name'));
        }

        return 0;
    }
}
