<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class authentication_log extends Model
{
    use HasFactory;
    use Prunable;

    /**
     * The model type
     *
     * @var string|null (node|central|node_pgsql|central_pgsql)
     */
    protected $modelType = 'central_pgsql';

    /**
     * Set connection for Node Model
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {

        $this->connection = 'centralpgsql';

        parent::__construct($attributes);
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subMonths(6));
    }
}
