<?php

namespace Tests\Unit;

use App\Models\Router;
use App\Services\RouterManagementService;
use PHPUnit\Framework\TestCase;

class FakeClient
{
    public $commands = [];
    public $responses = [];

    public function query($command, $arguments = [])
    {
        $this->commands[] = ['command' => $command, 'arguments' => $arguments];
        return $this;
    }

    public function read()
    {
        if (empty($this->responses)) {
            return [];
        }

        return array_shift($this->responses);
    }
}

class RouterManagementServiceTest extends TestCase
{
    public function test_get_ip_pools_returns_pools()
    {
        $router = new Router([
            'ip_address' => '127.0.0.1',
            'username' => 'admin',
            'password' => 'pass',
            'port' => 8728,
        ]);

        $client = new FakeClient();
        $client->responses[] = [
            ['name' => 'pool1', 'ranges' => '192.168.1.0/24'],
        ];

        $svc = new RouterManagementService($router, $client);

        $pools = $svc->getIpPools();

        $this->assertIsArray($pools);
        $this->assertNotEmpty($pools);
        $this->assertEquals('pool1', $pools[0]['name']);
    }

    public function test_suspend_customer_sends_expected_commands()
    {
        $router = new Router([
            'ip_address' => '127.0.0.1',
            'username' => 'admin',
            'password' => 'pass',
            'port' => 8728,
        ]);

        $client = new FakeClient();

        // Responses for print queries (ppp/secret/print and ppp/active/print)
        $client->responses[] = [
            ['.id' => '*1'],
        ];
        // for ppp/active/print
        $client->responses[] = [
            ['.id' => '*2'],
        ];

        $svc = new RouterManagementService($router, $client);

        $result = $svc->suspendCustomer('testuser', '192.0.2.10');

        $this->assertTrue($result['ok']);

        // verify that commands include add to address-list, disable secret, and remove active
        $commands = array_column($client->commands, 'command');

        $this->assertContains('/ip/firewall/address-list/add', $commands);
        $this->assertContains('/ppp/secret/disable', $commands);
        $this->assertContains('/ppp/active/remove', $commands);
    }
}
