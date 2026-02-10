<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Models\Router;
use EvilFreelancer\RouterOS\Client;
use EvilFreelancer\RouterOS\Config;
use EvilFreelancer\RouterOS\Exceptions\ConnectException;
use EvilFreelancer\RouterOS\Exceptions\ConfigException;
use EvilFreelancer\RouterOS\Exceptions\QueryException;

class MikrotikService
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Router
     */
    protected $router;

    /**
     * MikrotikService constructor.
     *
     * @param Router $router
     * @throws MikrotikServiceException
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->connect();
    }

    /**
     * Connect to the Mikrotik router.
     *
     * @throws MikrotikServiceException
     */
    protected function connect()
    {
        try {
            $config = new Config([
                'host' => $this->router->ip_address,
                'user' => $this->router->username,
                'pass' => $this->router->password,
                'port' => (int) $this->router->port,
            ]);

            $this->client = new Client($config);
        } catch (ConfigException | ConnectException $e) {
            Log::error('Failed to connect to Mikrotik router', [
                'router_id' => $this->router->id,
                'error' => $e->getMessage(),
            ]);

            throw new MikrotikServiceException('Failed to connect to the Mikrotik router.');
        }
    }

    /**
     * Get IP pools from the router.
     *
     * @return array
     * @throws MikrotikServiceException
     */
    public function getIpPools(): array
    {
        try {
            return $this->client->query('/ip/pool/print')->read();
        } catch (QueryException $e) {
            Log::error('Failed to get IP pools from Mikrotik router', [
                'router_id' => $this->router->id,
                'error' => $e->getMessage(),
            ]);

            throw new MikrotikServiceException('Failed to retrieve IP pools from the router.');
        }
    }

    /**
     * Get PPP profiles from the router.
     *
     * @return array
     * @throws MikrotikServiceException
     */
    public function getPppProfiles(): array
    {
        try {
            return $this->client->query('/ppp/profile/print')->read();
        } catch (QueryException $e) {
            Log::error('Failed to get PPP profiles from Mikrotik router', [
                'router_id' => $this->router->id,
                'error' => $e->getMessage(),
            ]);

            throw new MikrotikServiceException('Failed to retrieve PPP profiles from the router.');
        }
    }
}