<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsHistory extends Model
{
    use HasFactory;

    protected $table = 'sms_histories';

    protected $fillable = [
        'operator_id',
        'customer_id',
        'sms_gateway_id',
        'to_number',
        'sms_body',
        'status_text',
        'sms_cost',
        'date',
        'week',
        'month',
        'year',
    ];
}
