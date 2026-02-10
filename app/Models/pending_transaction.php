<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pending_transaction extends Model
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
     * Get the Sender that owns the Pending Transaction.
     */
    public function sender()
    {
        return $this->belongsTo(operator::class, 'account_provider', 'id');
    }


    /**
     * Get the Receiver that owns the Pending Transaction.
     */
    public function receiver()
    {
        return $this->belongsTo(operator::class, 'account_owner', 'id');
    }
}
