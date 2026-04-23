<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'organization_id',
        'shop_name',
        'address',
        'contact_number',
        'description',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function shopServices(): HasMany
    {
        return $this->hasMany(ShopService::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }
}
