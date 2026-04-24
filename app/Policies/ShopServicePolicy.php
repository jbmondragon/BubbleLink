<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;

class ShopServicePolicy
{
    public function create(User $user, Shop $shop): bool
    {
        return $this->managesShop($user, $shop->organization_id, $shop->id);
    }

    public function delete(User $user, ShopService $shopService): bool
    {
        $shopService->loadMissing('shop');

        return $this->managesShop($user, $shopService->shop->organization_id, $shopService->shop_id);
    }

    private function managesShop(User $user, int $organizationId, int $shopId): bool
    {
        return $user->memberships()
            ->where('organization_id', $organizationId)
            ->where('role', 'manager')
            ->where('shop_id', $shopId)
            ->exists();
    }
}
