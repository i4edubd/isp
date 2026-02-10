<?php

namespace App\Jobs;

use App\Http\Controllers\OperatorDeleteController;
use App\Models\operator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OperatorDeleteJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The operator instance.
     *
     * @var \App\Models\operator
     */
    public $operator;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->operator->id;
    }

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
        if ($operator->role == 'operator') {
            OperatorDeleteController::deleteOperator($operator);
        }
    }
}
