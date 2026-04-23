<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Shop;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function currentMembership(Request $request): ?Membership
    {
        $memberships = $request->user()
            ->memberships()
            ->with(['organization', 'shop'])
            ->orderByRaw("case role when 'owner' then 1 when 'manager' then 2 when 'staff' then 3 else 4 end")
            ->get();

        if ($memberships->isEmpty()) {
            return null;
        }

        $selectedOrganizationId = $request->session()->get('current_organization_id');

        if ($selectedOrganizationId !== null) {
            $selectedMembership = $memberships->firstWhere('organization_id', (int) $selectedOrganizationId);

            if ($selectedMembership) {
                return $selectedMembership;
            }

            $request->session()->forget('current_organization_id');
        }

        return $memberships->first();
    }

    protected function currentOrganization(Request $request): ?Organization
    {
        return $this->currentMembership($request)?->organization;
    }

    protected function currentRole(Request $request): ?string
    {
        return $this->currentMembership($request)?->role;
    }

    protected function organizationForRoles(Request $request, array $roles): ?Organization
    {
        $membership = $this->currentMembership($request);

        if (! $membership) {
            return null;
        }

        abort_unless(in_array($membership->role, $roles, true), 403);

        return $membership->organization;
    }

    protected function ownerOrganization(Request $request): ?Organization
    {
        return $this->organizationForRoles($request, ['owner']);
    }

    protected function ensureOwnerForOrganization(Request $request, int $organizationId): void
    {
        $this->ensureOrganizationRole($request, $organizationId, ['owner']);
    }

    protected function ensureOrganizationRole(Request $request, int $organizationId, array $roles): void
    {
        abort_unless(
            $request->user()->memberships()
                ->whereIn('role', $roles)
                ->where('organization_id', $organizationId)
                ->exists(),
            403
        );
    }

    protected function ensureOwnerForShop(Request $request, Shop $shop): void
    {
        $this->ensureOwnerForOrganization($request, $shop->organization_id);
    }

    protected function ensureShopRole(Request $request, Shop $shop, array $roles): void
    {
        $membershipQuery = $request->user()->memberships()->where('organization_id', $shop->organization_id);

        if (in_array('owner', $roles, true) && (clone $membershipQuery)->where('role', 'owner')->exists()) {
            return;
        }

        $scopedRoles = array_values(array_diff($roles, ['owner']));

        abort_unless(
            ! empty($scopedRoles)
            && (clone $membershipQuery)
                ->whereIn('role', $scopedRoles)
                ->where('shop_id', $shop->id)
                ->exists(),
            403
        );
    }

    protected function ensureOwnerForOrder(Request $request, Order $order): void
    {
        $order->loadMissing('shop');

        $this->ensureOwnerForShop($request, $order->shop);
    }

    protected function ensureOrderRole(Request $request, Order $order, array $roles): void
    {
        $order->loadMissing('shop');

        $this->ensureShopRole($request, $order->shop, $roles);
    }
}
