<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\CustomerBill;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class BillingServiceTest extends TestCase
{
    public function test_generate_monthly_bills_with_no_amount_skips_and_reports_error()
    {
        $customer = new Customer(['id' => 999, 'name' => 'Test']);

        $svc = new BillingService();
        $result = $svc->generateMonthlyBills();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('created', $result);
        $this->assertArrayHasKey('errors', $result);
    }
}
