<?php

namespace App\Jobs;

use App\Http\Controllers\GroupAdminController;
use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignDatabaseConnectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The operator instance.
     *
     * @var \App\Models\operator
     */
    public $operator;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(operator $operator)
    {
        $this->operator = $operator;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $operator = $this->operator;
        if ($operator->role !== 'group_admin') {
            return 0;
        }
        $controller = new GroupAdminController();
        $radius_db_connection = $controller->assignDatabaseConnection();
        $operator->radius_db_connection = $radius_db_connection;
        $operator->save();
        return 0;
    }
}
