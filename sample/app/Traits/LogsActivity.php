<?php

namespace App\Traits;

use App\Models\activity_log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity
     *
     * @param string $topic
     * @param string $log
     * @param int|null $customerId
     * @return void
     */
    protected function logActivity($topic, $log, $customerId = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        // Determine gid based on user role
        $gid = $user->gid ?? $user->id;
        
        // Determine operator_id
        $operatorId = $user->id;

        activity_log::create([
            'gid' => $gid,
            'operator_id' => $operatorId,
            'customer_id' => $customerId,
            'topic' => $topic,
            'year' => Carbon::now()->format('Y'),
            'month' => Carbon::now()->format('m'),
            'week' => Carbon::now()->format('W'),
            'log' => $log,
        ]);
    }
}
