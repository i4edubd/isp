<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class event_sms extends Model
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
    protected $appends = ['color'];

    /**
     * Get color attribute
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function color(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['status'] == 'disabled') {
                    return "text-danger";
                }
                return "text-success";
            },
        );
    }
}
