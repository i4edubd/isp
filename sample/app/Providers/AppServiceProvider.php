<?php

namespace App\Providers;

use App\Http\Controllers\TelegramEmergencyNotificationController;
use App\Models\operator;
use App\Observers\OperatorObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Register observer for auto-creating default departments
        operator::observe(OperatorObserver::class);

        Queue::failing(function (JobFailed $event) {
            $message = 'A job failed in ' . config('app.url') . ' >> Queue : ' . $event->job->getQueue();
            TelegramEmergencyNotificationController::send($message);
        });
    }
}
