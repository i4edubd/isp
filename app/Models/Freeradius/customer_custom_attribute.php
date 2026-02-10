<?php

namespace App\Models\Freeradius;

use App\Models\custom_field;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class customer_custom_attribute extends Model
{
    use HasFactory;

    /**
     * The model type
     *
     * @var string|null (node|central)
     */
    protected $modelType = 'node';

    /**
     * Set connection for Node Model
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (Auth::user()) {

            $operator = Auth::user();

            $this->connection = $operator->radius_db_connection;
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name'];

    /**
     * Get the attribute name.
     *
     * * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => custom_field::where('id', $attributes['custom_field_id'])->firstOr(function () {
                return custom_field::make([
                    'id' => 0,
                    'operator_id' => 0,
                    'name' => 0,
                ]);
            })->name,
        )->shouldCache();
    }
}
