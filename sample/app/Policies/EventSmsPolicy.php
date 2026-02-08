<?php

namespace App\Policies;

use App\Models\event_sms;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventSmsPolicy
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
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\event_sms  $eventSms
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, event_sms $eventSms)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($eventSms->operator_id == 0) {
            return true;
        }

        if ($operator->id == $eventSms->operator_id) {
            return true;
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
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\event_sms  $eventSms
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, event_sms $eventSms)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($eventSms->operator_id == 0) {
            return true;
        }

        if ($operator->id == $eventSms->operator_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\event_sms  $eventSms
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, event_sms $eventSms)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\event_sms  $eventSms
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(operator $operator, event_sms $eventSms)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\event_sms  $eventSms
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(operator $operator, event_sms $eventSms)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\event_sms  $eventSms
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateStatus(operator $operator, event_sms $eventSms)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($eventSms->operator_id == 0) {
            return false;
        }

        if ($operator->id !== $eventSms->operator_id) {
            return false;
        }

        switch ($eventSms->event) {
            case 'OTP':
                return false;
                break;

            case 'CUSTOMER_ID':
                return false;
                break;

            case 'SEND_MONEY_NOTIFICATION':
                return false;
                break;

            default:
                return true;
                break;
        }
    }
}
