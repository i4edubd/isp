<?php

namespace App\Models;

use App\Models\Freeradius\nas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_import_request extends Model
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

    /**
     * Get the group admin associated with the request.
     */

    public function groupAdmin()
    {
        return $this->belongsTo(operator::class, 'mgid', 'id');
    }

    /**
     * Get the operator associated with the request.
     */
    public function operator()
    {
        return $this->belongsTo(operator::class);
    }

    /**
     * Get the router associated with the request.
     */
    public function router()
    {
        return $this->belongsTo(nas::class, 'nas_id', 'id')->withDefault();
    }

    /**
     * Get the billing profile associated with the request.
     */
    public function billingProfile()
    {
        return $this->belongsTo(billing_profile::class)->withDefault();
    }

    /**
     * Get the reports associated with the request.
     */
    public function reports()
    {
        return $this->hasMany(customer_import_report::class, 'request_id', 'id');
    }
}
