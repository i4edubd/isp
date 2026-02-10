<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBill extends Model
{
    protected $table = 'customer_bills';

    protected $fillable = [
        'mgid','gid','operator_id','customer_id','package_id','validity_period',
        'customer_zone_id','name','mobile','username','amount','description',
        'billing_period','due_date','year','month','status'
    ];
}
