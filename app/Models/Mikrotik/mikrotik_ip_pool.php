<?php

namespace App\Models\Mikrotik;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mikrotik_ip_pool extends Model
{
    use HasFactory;

    /**
     * The model type
     *
     * @var string|null (node|central)
     */
    protected $modelType = 'central';

    /**
     * Set connection for Central Model if (host_type === 'node')
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (config('local.host_type', 'central') === 'node') {
            if ($this->modelType === 'central') {
                $this->connection = config('database.central', 'mysql');
            }
        }

        parent::__construct($attributes);
    }
}
