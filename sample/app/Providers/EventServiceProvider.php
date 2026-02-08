<?php

namespace App\Providers;

use App\Events\ImportPppCustomersRequested;
use App\Listeners\FailedLoginListener;
use App\Listeners\ImportPppCustomers;
use App\Models\account;
use App\Models\Freeradius\customer;
use App\Observers\AccountObserver;
use App\Observers\CustomerObserver;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        ImportPppCustomersRequested::class => [
            ImportPppCustomers::class,
        ],

        Failed::class => [
            FailedLoginListener::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        customer::observe(CustomerObserver::class);
        account::observe(AccountObserver::class);
    }
}
