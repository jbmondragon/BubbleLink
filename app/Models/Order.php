<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'customer_id',
        'shop_id',
        'shop_service_id',
        'service_mode',
        'pickup_address',
        'delivery_address',
        'number_of_loads',
        'pickup_datetime',
        'delivery_datetime',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'shop_rating',
        'rated_at',
    ];

    protected function casts(): array
    {
        return [
            'number_of_loads' => 'integer',
            'total_price' => 'decimal:2',
            'pickup_datetime' => 'datetime',
            'delivery_datetime' => 'datetime',
            'rated_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function shopService(): BelongsTo
    {
        return $this->belongsTo(ShopService::class);
    }
}
