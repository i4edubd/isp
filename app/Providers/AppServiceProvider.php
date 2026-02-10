<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Customer;
use App\Observers\CustomerObserver;
use App\Services\Sms\SmsGatewayInterface;
use App\Services\Sms\CompositeGateway;
use App\Services\Sms\MaestroGateway;
use Illuminate\Support\Facades\Config;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\DummyGateway;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind SMS gateway interface to a composite implementation using config order
        $this->app->singleton(SmsGatewayInterface::class, function ($app) {
            $cfg = Config::get('sms.gateways', []);
            $classes = Config::get('sms.classes', []);

            $instances = [];
            foreach ($cfg as $key) {
                $class = $classes[$key] ?? null;
                if ($class && class_exists($class)) {
                    $instances[] = new $class();
                }
            }

            // Fallback to Maestro if none configured
            if (empty($instances)) {
                $instances[] = new MaestroGateway();
            }

            return new CompositeGateway($instances);
        });

        // Bind a dummy payment gateway - replace with real gateway bindings per environment
        $this->app->singleton(PaymentGatewayInterface::class, function ($app) {
            return new DummyGateway();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Customer::observe(CustomerObserver::class);
    }
}
