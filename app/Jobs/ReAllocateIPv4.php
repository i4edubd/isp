<?php

namespace App\Jobs;

use App\Http\Controllers\IPv4ReAllocateController;
use App\Models\ipv4pool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReAllocateIPv4 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The ipv4pool instance
     *
     * @var \App\Models\ipv4pool
     */
    protected $ipv4pool;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ipv4pool $ipv4pool)
    {
        $this->ipv4pool = $ipv4pool;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ipv4pool = $this->ipv4pool;
        IPv4ReAllocateController::store($ipv4pool);
    }
}
