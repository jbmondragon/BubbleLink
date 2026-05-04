<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    public const DEFAULT_SERVICE_NAMES = [
        'Wash, Dry, Fold',
        'Dry Cleaning',
        'Ironing Only',
        'Wash, Dry, Fold, Iron',
    ];

    public $timestamps = false;

    protected $fillable = [
        'shop_id',
        'name',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function shopServices(): HasMany
    {
        return $this->hasMany(ShopService::class);
    }

    public static function ensureDefaultServicesForShop(Shop $shop): void
    {
        foreach (self::DEFAULT_SERVICE_NAMES as $serviceName) {
            self::query()->firstOrCreate([
                'shop_id' => $shop->id,
                'name' => $serviceName,
            ]);
        }
    }
}
