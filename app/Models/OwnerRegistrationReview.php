<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnerRegistrationReview extends Model
{
    protected $fillable = [
        'shop_owner_user_id',
        'platform_admin_user_id',
        'action',
        'previous_status',
        'new_status',
    ];

    public function shopOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shop_owner_user_id');
    }

    public function platformAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'platform_admin_user_id');
    }
}
