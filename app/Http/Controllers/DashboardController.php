<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $organization = $this->currentOrganization($request);
        $membership = $this->currentMembership($request);
        $currentRole = $this->currentRole($request);

        $shops = $organization && $membership
            ? $organization->shops()->when(
                $currentRole !== 'owner',
                fn ($query) => $query->whereKey($membership->shop_id ?? 0)
            )->with([
                'orders',
                'shopServices',
            ])->get()
            : collect();

        return view('dashboard', [
            'organization' => $organization,
            'currentMembership' => $membership,
            'currentRole' => $currentRole,
            'shops' => $shops,
            'shopCount' => $shops->count(),
            'totalOrders' => $shops->flatMap->orders->count(),
            'totalRevenue' => $shops->flatMap->orders->sum('total_price'),
            'memberCount' => $organization ? $organization->memberships()->count() : 0,
            'serviceTypeCount' => $organization ? $organization->services()->count() : 0,
            'assignedServiceCount' => $shops->flatMap->shopServices->count(),
            'canManageServices' => $organization && $currentRole === 'manager',
            'canManageOrders' => $organization && in_array($currentRole, ['manager', 'staff'], true),
            'canManageMemberships' => $organization && $currentRole === 'owner',
            'canManageShops' => $organization && $currentRole === 'owner',
        ]);
    }
}
