<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Radcheck;
use App\Models\Radreply;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // Add a basic password check entry for the new user.
        Radcheck::create([
            'username' => $customer->username,
            'attribute' => 'Cleartext-Password',
            'op' => ':=',
            'value' => $customer->password,
        ]);

        // Prevent simultaneous sessions.
        Radcheck::create([
            'username' => $customer->username,
            'attribute' => 'Simultaneous-Use',
            'op' => ':=',
            'value' => '1',
        ]);

        // Add MAC address binding if a MAC address is provided.
        if ($customer->mac_address) {
            Radcheck::create([
                'username' => $customer->username,
                'attribute' => 'Calling-Station-Id',
                'op' => '==',
                'value' => $customer->mac_address,
            ]);
        }
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        // Check if the user is being suspended or activated.
        if ($customer->isDirty('is_active')) {
            if (!$customer->is_active) {
                // To disable a user, a common method is to set an Auth-Type of Reject.
                Radcheck::updateOrCreate(
                    ['username' => $customer->username, 'attribute' => 'Auth-Type'],
                    ['op' => ':=', 'value' => 'Reject']
                );
            } else {
                // If re-activated, remove the Auth-Type Reject entry.
                Radcheck::where('username', $customer->username)
                         ->where('attribute', 'Auth-Type')
                         ->delete();
            }
        }
        
        // Check if the password was changed.
        if ($customer->isDirty('password')) {
            Radcheck::updateOrCreate(
                ['username' => $customer->username, 'attribute' => 'Cleartext-Password'],
                ['op' => ':=', 'value' => $customer->password]
            );
        }

        // Check if the MAC address was changed.
        if ($customer->isDirty('mac_address')) {
            if ($customer->mac_address) {
                // Update or create the MAC address binding.
                Radcheck::updateOrCreate(
                    ['username' => $customer->username, 'attribute' => 'Calling-Station-Id'],
                    ['op' => '==', 'value' => $customer->mac_address]
                );
            } else {
                // If the MAC address is removed, delete the binding.
                Radcheck::where('username', $customer->username)
                         ->where('attribute', 'Calling-Station-Id')
                         ->delete();
            }
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        // When a customer is deleted, remove all their related radcheck and radreply entries.
        Radcheck::where('username', $customer->username)->delete();
        Radreply::where('username', $customer->username)->delete();
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}
