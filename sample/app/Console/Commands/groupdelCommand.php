<?php

namespace App\Console\Commands;

use App\Jobs\GroupAdminDeleteJob;
use App\Models\operator;
use Illuminate\Console\Command;

class groupdelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'groupdel {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete(remove) a given group from the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $operator = operator::findOrFail($this->argument('mgid'));

        $info = "Name: " . $operator->name . " Email: " . $operator->email . " Company: " . $operator->company;
        $this->info($info);

        // confirm before process
        if ($this->confirm('Do you wish to continue?') == false) {
            return 0;
        }

        // is group_admin
        if ($operator->role !== 'group_admin') {
            $this->info('Not Group Admin');
            return 0;
        }

        // process
        $operator->deleting = 1;
        $operator->save();

        GroupAdminDeleteJob::dispatch($operator)
            ->onConnection('database')
            ->onQueue('default');

        $this->info('processing...');

        return 0;
    }
}
