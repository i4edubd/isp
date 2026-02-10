<?php

namespace App\Services;

use App\Models\Router;
use Illuminate\Support\Facades\Log;
use EvilFreelancer\RouterOS\Client;
use EvilFreelancer\RouterOS\Config;
use EvilFreelancer\RouterOS\Exceptions\ConnectException;
use EvilFreelancer\RouterOS\Exceptions\ConfigException;
use EvilFreelancer\RouterOS\Exceptions\QueryException;

class RouterManagementService
{
    protected Router $router;
    protected Client $client;

    /**
     * Accept optional Client for easier testing.
     */
    public function __construct(Router $router, ?Client $client = null)
    {
        $this->router = $router;
        if ($client !== null) {
            $this->client = $client;
        } else {
            $this->connect();
        }
    }

    protected function connect(): void
    {
        try {
            $cfg = new Config([
                'host' => $this->router->ip_address,
                'user' => $this->router->username,
                'pass' => $this->router->password,
                'port' => (int) ($this->router->port ?? 8728),
            ]);

            $this->client = new Client($cfg);
        } catch (ConfigException | ConnectException $e) {
            Log::error('Router connect failed', [
                'router_id' => $this->router->id,
                'error' => $e->getMessage(),
            ]);

            throw new MikrotikServiceException('Failed to connect to router');
        }
    }

    /**
     * Provision a PPPoE/PPP secret on the router.
     * $data should contain at least: name, password, service (pppoe), profile
     */
    public function provisionPppSecret(array $data): array
    {
        $defaults = [
            'service' => 'pppoe',
            'disabled' => 'no',
        ];

        $payload = array_merge($defaults, $data);

        try {
            $this->safeQuery('/ppp/secret/add', $payload);

            return ['ok' => true];
        } catch (MikrotikServiceException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function removePppSecret(string $name): array
    {
        try {
            $items = $this->safeQuery('/ppp/secret/print', ['?name' => $name, '.proplist' => '.id']);

            foreach ($items as $item) {
                $this->safeQuery('/ppp/secret/remove', ['.id' => $item['.id']]);
            }

            return ['ok' => true];
        } catch (MikrotikServiceException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function suspendCustomer(string $username, ?string $ipAddress = null): array
    {
        try {
            // add to firewall suspended list
            if ($ipAddress) {
                $this->safeQuery('/ip/firewall/address-list/add', [
                    'list' => config('mikrotik.firewall.suspended_list_name', 'suspended'),
                    'address' => $ipAddress,
                    'comment' => $username,
                ]);
            }

            // disable ppp secret if exists
            $items = $this->safeQuery('/ppp/secret/print', ['?name' => $username, '.proplist' => '.id']);
            foreach ($items as $it) {
                $this->safeQuery('/ppp/secret/disable', ['.id' => $it['.id']]);
            }

            // disconnect active sessions
            $this->safeQuery('/ppp/active/print', ['?name' => $username, '.proplist' => '.id']);
            $active = $this->client->read();
            foreach ($active as $a) {
                $this->safeQuery('/ppp/active/remove', ['.id' => $a['.id']]);
            }

            return ['ok' => true];
        } catch (MikrotikServiceException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function resumeCustomer(string $username, ?string $ipAddress = null): array
    {
        try {
            // remove firewall list entry if ip provided
            if ($ipAddress) {
                $this->safeQuery('/ip/firewall/address-list/print', ['?address' => $ipAddress, '.proplist' => '.id']);
                $entries = $this->client->read();
                foreach ($entries as $e) {
                    $this->safeQuery('/ip/firewall/address-list/remove', ['.id' => $e['.id']]);
                }
            }

            // enable ppp secret
            $items = $this->safeQuery('/ppp/secret/print', ['?name' => $username, '.proplist' => '.id']);
            foreach ($items as $it) {
                $this->safeQuery('/ppp/secret/enable', ['.id' => $it['.id']]);
            }

            return ['ok' => true];
        } catch (MikrotikServiceException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Retrieve IP pools from the router.
     */
    public function getIpPools(): array
    {
        try {
            return $this->safeQuery('/ip/pool/print');
        } catch (MikrotikServiceException $e) {
            Log::error('Failed to fetch ip pools', ['router_id' => $this->router->id, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Retrieve PPP profiles from the router.
     */
    public function getPppProfiles(): array
    {
        try {
            return $this->safeQuery('/ppp/profile/print');
        } catch (MikrotikServiceException $e) {
            Log::error('Failed to fetch ppp profiles', ['router_id' => $this->router->id, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Run a query with centralized error handling and optional retries.
     * Returns the raw read() array when appropriate.
     *
     * @throws MikrotikServiceException
     */
    protected function safeQuery(string $command, array $arguments = [], int $retries = 1): array
    {
        $attempt = 0;
        beginning:
        $attempt++;

        try {
            $this->client->query($command, $arguments)->read();

            // many RouterOS print commands return rows via read();
            // read whatever is in the buffer and return.
            $res = $this->client->read();
            return $res ?? [];
        } catch (QueryException $e) {
            Log::warning('Mikrotik query failed', [
                'router_id' => $this->router->id,
                'command' => $command,
                'args' => $arguments,
                'attempt' => $attempt,
                'error' => $e->getMessage(),
            ]);

            if ($attempt <= $retries) {
                goto beginning;
            }

            throw new MikrotikServiceException('Router query failed: ' . $e->getMessage());
        }
    }
}

