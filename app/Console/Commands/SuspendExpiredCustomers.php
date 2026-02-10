<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\RouterManagementService;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SuspendExpiredCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:handle-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify customers before package expiration and suspend those whose packages have expired';

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->notifyExpiringCustomers();
        $this->suspendExpiredCustomers();
        return 0;
    }

    protected function notifyExpiringCustomers()
    {
        $this->info('Checking for customers with packages expiring soon...');
        $notificationDate = Carbon::now()->addDays(3)->toDateString();

        $expiringCustomers = Customer::where('is_active', true)
            ->where('package_expiry_date', $notificationDate)
            ->get();

        if ($expiringCustomers->isEmpty()) {
            $this->info('No customers to notify today.');
            return;
        }

        foreach ($expiringCustomers as $customer) {
            $message = "Dear {$customer->name}, your internet package will expire on {$customer->package_expiry_date}. Please recharge to avoid service interruption.";
            $success = $this->smsService->send($customer->mobile, $message);

            if ($success) {
                $this->line("Successfully sent expiration notice to #{$customer->id} ({$customer->username}).");
            } else {
                $this->error("Failed to send expiration notice to #{$customer->id} ({$customer->username}).");
            }
        }
    }
    
    protected function suspendExpiredCustomers()
    {
        $this->info('Starting suspension process for expired customers...');

        $today = Carbon::now()->toDateString();

        $expiredCustomers = Customer::where('is_active', true)
            ->where('package_expiry_date', '<', $today)
            ->get();

        if ($expiredCustomers->isEmpty()) {
            $this->info('No expired customers to suspend.');
            return;
        }

        foreach ($expiredCustomers as $customer) {
            $customer->is_active = false;
            $customer->save();
            $this->line("Suspended customer #{$customer->id} ({$customer->username}) in database.");

            try {
                $routerService = new RouterManagementService($customer->router);
                $routerService->suspendCustomer($customer->username, $customer->ip_address);
                $this->line("Suspended customer on router #{$customer->id} ({$customer->username}).");
            } catch (\Exception $e) {
                $this->error("Failed to suspend customer on router #{$customer->id} ({$customer->username}): " . $e->getMessage());
            }
        }

        $this->info('Finished suspending expired customers.');
    }
}
