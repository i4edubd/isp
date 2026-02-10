<?php

namespace Tests\Unit;

use App\Services\Sms\CompositeGateway;
use App\Services\Sms\SmsGatewayInterface;
use PHPUnit\Framework\TestCase;

class DummySuccessGateway implements SmsGatewayInterface
{
    public function send(string $to, string $message): bool
    {
        return true;
    }
}

class DummyFailGateway implements SmsGatewayInterface
{
    public function send(string $to, string $message): bool
    {
        throw new \Exception('Simulated failure');
    }
}

class CompositeSmsGatewayTest extends TestCase
{
    public function test_composite_returns_true_when_first_successful()
    {
        $svc = new CompositeGateway([new DummySuccessGateway(), new DummyFailGateway()]);

        $this->assertTrue($svc->send('12345', 'hello'));
    }

    public function test_composite_falls_back_on_failure()
    {
        $svc = new CompositeGateway([new DummyFailGateway(), new DummySuccessGateway()]);

        $this->assertTrue($svc->send('12345', 'hello'));
    }

    public function test_composite_returns_false_when_all_fail()
    {
        $svc = new CompositeGateway([new DummyFailGateway()]);

        $this->assertFalse($svc->send('12345', 'hello'));
    }
}
