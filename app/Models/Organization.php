<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'owner_user_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
