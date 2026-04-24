<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function create(User $user, Organization $organization): bool
    {
        return $user->memberships()
            ->where('organization_id', $organization->id)
            ->where('role', 'owner')
            ->exists();
    }

    public function view(User $user, Shop $shop): bool
    {
        $membershipQuery = $user->memberships()->where('organization_id', $shop->organization_id);

        return (clone $membershipQuery)->where('role', 'owner')->exists()
            || (clone $membershipQuery)
                ->whereIn('role', ['manager', 'staff'])
                ->where('shop_id', $shop->id)
                ->exists();
    }

    public function update(User $user, Shop $shop): bool
    {
        return $user->memberships()
            ->where('organization_id', $shop->organization_id)
            ->where('role', 'owner')
            ->exists();
    }

    public function delete(User $user, Shop $shop): bool
    {
        return $this->update($user, $shop);
    }
}
