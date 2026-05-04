<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function create(User $user): bool
    {
        return ! $user->is_platform_admin && $user->isApprovedShopOwnerRegistration();
    }

    public function view(User $user, Shop $shop): bool
    {
        return $shop->owner_user_id === $user->id;
    }

    public function update(User $user, Shop $shop): bool
    {
        return $shop->owner_user_id === $user->id;
    }

    public function delete(User $user, Shop $shop): bool
    {
        return $shop->owner_user_id === $user->id;
    }
}
