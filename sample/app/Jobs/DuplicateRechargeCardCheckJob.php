<?php

namespace App\Jobs;

use App\Models\operator;
use App\Models\recharge_card;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DuplicateRechargeCardCheckJob implements ShouldQueue
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

        $recharge_cards = recharge_card::where('operator_id', $operator->id)
            ->where('status', 'unused')
            ->get();

        foreach ($recharge_cards as $recharge_card) {
            if ($recharge_cards->where('pin', $recharge_card->pin)->count() > 1) {
                recharge_card::where('operator_id', $operator->id)
                    ->where('pin', $recharge_card->pin)
                    ->delete();
                $info = 'Duplicate Recharge Card Deleted >> operator ID: ' . $operator->id . ' pin : ' . $recharge_card->pin;
                Log::channel('debug')->debug($info);
            }
        }
    }
}
