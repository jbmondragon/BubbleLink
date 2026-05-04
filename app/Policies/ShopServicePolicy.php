<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;

class ShopServicePolicy
{
    public function create(User $user, Shop $shop): bool
    {
        return $shop->owner_user_id === $user->id;
    }

    public function delete(User $user, ShopService $shopService): bool
    {
        $shopService->loadMissing('shop');

        return $shopService->shop->owner_user_id === $user->id;
    }
}
