<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $order->customer_id === $user->id || $this->hasShopOrderAccess($user, $order->shop);
    }

    public function create(User $user, Shop $shop): bool
    {
        return $this->hasShopOrderAccess($user, $shop);
    }

    public function update(User $user, Order $order): bool
    {
        return $this->hasShopOrderAccess($user, $order->shop);
    }

    public function rate(User $user, Order $order): bool
    {
        return $order->customer_id === $user->id && $order->status === 'completed';
    }

    private function hasShopOrderAccess(User $user, Shop $shop): bool
    {
        return $user->memberships()
            ->where('organization_id', $shop->organization_id)
            ->whereIn('role', ['manager', 'staff'])
            ->where('shop_id', $shop->id)
            ->exists();
    }
}
