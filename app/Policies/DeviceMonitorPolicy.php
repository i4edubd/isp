<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\device_monitor;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeviceMonitorPolicy
{
    use HandlesAuthorization;

    /**
     * Check if the operator role is allowed to access Device Monitoring
     *
     * @param  \App\Models\operator  $operator
     * @return bool
     */
    private function isAllowedRole(operator $operator)
    {
        return in_array($operator->role, ['super_admin', 'developer', 'group_admin']);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(operator $operator)
    {
        // Only super_admin, developer, and group_admin can access device monitors
        // Exclude operators and suboperators
        return $this->isAllowedRole($operator);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\device_monitor  $deviceMonitor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, device_monitor $deviceMonitor)
    {
        // Only allowed roles can view device monitors
        if (!$this->isAllowedRole($operator)) {
            return false;
        }

        // Super admin and developer can view all devices
        if (in_array($operator->role, ['super_admin', 'developer'])) {
            return true;
        }

        // Group admin can view devices from their group
        if ($operator->role === 'group_admin') {
            return $deviceMonitor->gid === $operator->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(operator $operator)
    {
        // Only super_admin, developer, and group_admin can create device monitors
        // Exclude operators and suboperators
        return $this->isAllowedRole($operator);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\device_monitor  $deviceMonitor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, device_monitor $deviceMonitor)
    {
        // Only allowed roles can update device monitors
        if (!$this->isAllowedRole($operator)) {
            return false;
        }

        // Super admin and developer can update all devices
        if (in_array($operator->role, ['super_admin', 'developer'])) {
            return true;
        }

        // Group admin can update devices from their group
        if ($operator->role === 'group_admin') {
            return $deviceMonitor->gid === $operator->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\device_monitor  $deviceMonitor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, device_monitor $deviceMonitor)
    {
        // Only allowed roles can delete device monitors
        if (!$this->isAllowedRole($operator)) {
            return false;
        }

        // Super admin and developer can delete all devices
        if (in_array($operator->role, ['super_admin', 'developer'])) {
            return true;
        }

        // Group admin can delete devices from their group
        if ($operator->role === 'group_admin') {
            return $deviceMonitor->gid === $operator->id;
        }

        return false;
    }
}
