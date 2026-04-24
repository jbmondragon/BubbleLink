<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerShopController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $memberships = $request->user()?->memberships()->get(['organization_id', 'shop_id', 'role']) ?? collect();
        $ownerOrganizationIds = $memberships->where('role', 'owner')->pluck('organization_id')->filter()->unique()->values();
        $assignedShopIds = $memberships->whereIn('role', ['manager', 'staff'])->pluck('shop_id')->filter()->unique()->values();

        $shops = Shop::query()
            ->with(['organization', 'shopServices.service'])
            ->withCount(['orders as ratings_count' => fn ($query) => $query->whereNotNull('shop_rating')])
            ->withAvg(['orders as average_rating' => fn ($query) => $query->whereNotNull('shop_rating')], 'shop_rating')
            ->when($memberships->isNotEmpty(), function ($query) use ($ownerOrganizationIds, $assignedShopIds) {
                $query->where(function ($builder) use ($ownerOrganizationIds, $assignedShopIds) {
                    if ($ownerOrganizationIds->isNotEmpty()) {
                        $builder->whereIn('organization_id', $ownerOrganizationIds);
                    }

                    if ($assignedShopIds->isNotEmpty()) {
                        $method = $ownerOrganizationIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                        $builder->{$method}('id', $assignedShopIds);
                    }
                });
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('shop_name', 'like', '%'.$search.'%')
                        ->orWhere('address', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('shop_name')
            ->get();

        return view('customer.shops.index', [
            'shops' => $shops,
            'search' => $search,
        ]);
    }

    public function show(Shop $shop): View
    {
        $shop = Shop::query()
            ->with(['organization', 'shopServices.service'])
            ->withCount(['orders as ratings_count' => fn ($query) => $query->whereNotNull('shop_rating')])
            ->withAvg(['orders as average_rating' => fn ($query) => $query->whereNotNull('shop_rating')], 'shop_rating')
            ->findOrFail($shop->id);

        return view('customer.shops.show', [
            'shop' => $shop,
            'services' => $shop->shopServices->sortBy(fn ($shopService) => $shopService->service->name)->values(),
        ]);
    }
}
