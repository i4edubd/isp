<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\activity_log;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(operator $operator)
    {
        // All authenticated users can view activity logs (filtered by controller)
        return in_array($operator->role, ['super_admin', 'developer', 'group_admin', 'operator', 'sub_operator', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\activity_log  $activityLog
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, activity_log $activityLog)
    {
        // Super admin and developer can view all logs
        if (in_array($operator->role, ['super_admin', 'developer'])) {
            return true;
        }

        // Group admin can view logs from their group
        if ($operator->role === 'group_admin') {
            return $activityLog->gid === $operator->id;
        }

        // Others can only view their own logs
        return $activityLog->operator_id === $operator->id;
    }
}
