<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'shop_id',
        'service_id',
        'service_mode',
        'pickup_address',
        'delivery_address',
        'pickup_datetime',
        'total_price',
        'status',
        'payment_status',
    ];
}
