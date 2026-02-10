<?php

namespace App\Policies;

use App\Models\expiration_notifier;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpirationNotifierPolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expiration_notifier  $expirationNotifier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, expiration_notifier $expirationNotifier)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(operator $operator)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expiration_notifier  $expirationNotifier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, expiration_notifier $expirationNotifier)
    {
        if ($operator->id == $expirationNotifier->operator_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expiration_notifier  $expirationNotifier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, expiration_notifier $expirationNotifier)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expiration_notifier  $expirationNotifier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(operator $operator, expiration_notifier $expirationNotifier)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expiration_notifier  $expirationNotifier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(operator $operator, expiration_notifier $expirationNotifier)
    {
        //
    }
}
