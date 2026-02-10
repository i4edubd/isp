<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_bills_summary extends Model
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
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * Get the reseller that owns the customer_bills_summary.
     */
    public function reseller()
    {
        return $this->belongsTo(operator::class, 'reseller_id', 'id')->withDefault();
    }

    /**
     * Get the sub_reseller that owns the customer_bills_summary.
     */
    public function sub_reseller()
    {
        return $this->belongsTo(operator::class, 'sub_reseller_id', 'id')->withDefault();
    }

    /**
     * Get the package that owns the customer_bills_summary.
     */
    public function package()
    {
        return $this->belongsTo(package::class, 'package_id', 'id')->withDefault();
    }
}
