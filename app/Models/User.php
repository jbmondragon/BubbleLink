<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact_number',
        'is_platform_admin',
        'owner_registration_status',
        'approved_by_user_id',
        'owner_registration_reviewed_at',
        'pending_shop_name',
        'pending_shop_address',
        'pending_shop_contact_number',
        'pending_shop_description',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_platform_admin' => 'bool',
            'owner_registration_reviewed_at' => 'datetime',
        ];
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by_user_id');
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class, 'owner_user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function isPendingShopOwnerApproval(): bool
    {
        return $this->owner_registration_status === 'pending';
    }

    public function isRejectedShopOwnerRegistration(): bool
    {
        return $this->owner_registration_status === 'rejected';
    }

    public function isApprovedShopOwnerRegistration(): bool
    {
        return $this->owner_registration_status === 'approved';
    }
}
