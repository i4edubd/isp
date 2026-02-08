<?php

namespace App\Console\Commands;

use App\Models\Freeradius\customer;
use Illuminate\Console\Command;

class UpdateParentIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:parent_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update parent_id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customers = customer::where('parent_id', 0)->get();
        $bar = $this->output->createProgressBar(count($customers));
        $bar->start();

        foreach ($customers as $customer) {
            $customer->parent_id = $customer->id;
            $customer->save();
            $bar->advance();
        }

        $bar->finish();
        return 0;
    }
}
