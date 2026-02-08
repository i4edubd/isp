<?php

namespace App\Policies;

use App\Models\cash_out;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashOutPolicy
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
     * @param  \App\Models\cash_out  $cashOut
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, cash_out $cashOut)
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
     * @param  \App\Models\cash_out  $cashOut
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, cash_out $cashOut)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\cash_out  $cashOut
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, cash_out $cashOut)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\cash_out  $cashOut
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(operator $operator, cash_out $cashOut)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\cash_out  $cashOut
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(operator $operator, cash_out $cashOut)
    {
        //
    }
}
