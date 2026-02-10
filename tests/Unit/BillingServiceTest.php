<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\CustomerBill;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_monthly_bills_creates_records()
    {
        // Create a customer with package_price
        $customer = \App\Models\Customer::create([
            'name' => 'Test User',
            'is_active' => true,
            'package_price' => 100,
        ]);

        $svc = new BillingService();
        $result = $svc->generateMonthlyBills();

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['created']);
        $this->assertCount(0, $result['errors']);

        $this->assertDatabaseHas('customer_bills', ['customer_id' => $customer->id]);
    }
}
