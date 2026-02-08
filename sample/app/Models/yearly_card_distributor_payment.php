<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class yearly_card_distributor_payment extends Model
{
    use HasFactory;

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['distributor'];

    /**
     * Get the card_distributor that owns the phone.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function distributor(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return  card_distributor::find($attributes['card_distributor_id']);
            },
        );
    }
}
