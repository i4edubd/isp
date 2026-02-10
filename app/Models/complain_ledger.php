<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class complain_ledger extends Model
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
     * Get the receiver that owns the complaint.
     */
    public function operator()
    {
        return $this->belongsTo(operator::class, 'operator_id', 'id')->withDefault();
    }

    /**
     * Get the Category that owns the complaint.
     */
    public function fromCategory()
    {
        return $this->belongsTo(complain_category::class, 'fcid', 'id')->withDefault();
    }

    /**
     * Get the Category that owns the complaint.
     */
    public function toCategory()
    {
        return $this->belongsTo(complain_category::class, 'tcid', 'id')->withDefault();
    }


    /**
     * Get the Department that owns the complaint.
     */
    public function fromDepartment()
    {
        return $this->belongsTo(department::class, 'fdid', 'id')->withDefault();
    }

    /**
     * Get the Department that owns the complaint.
     */
    public function toDepartment()
    {
        return $this->belongsTo(department::class, 'tdid', 'id')->withDefault();
    }

}
