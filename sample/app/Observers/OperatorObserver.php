<?php

namespace App\Observers;

use App\Models\operator;
use App\Models\department;

class OperatorObserver
{
    /**
     * Handle the operator "created" event.
     *
     * @param  \App\Models\operator  $operator
     * @return void
     */
    public function created(operator $operator)
    {
        // Only create default departments for group_admin and operator roles
        if (in_array($operator->role, ['group_admin', 'operator'])) {
            $defaultDepartments = ['Billing', 'Sales', 'Support'];

            foreach ($defaultDepartments as $departmentName) {
                department::create([
                    'operator_id' => $operator->id,
                    'name' => $departmentName,
                ]);
            }
        }
    }
}
