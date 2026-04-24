<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function show(Request $request, Shop $shop): View
    {
        Gate::authorize('view', $shop);
        $currentRole = $this->currentRole($request);

        $shop->load([
            'organization',
            'shopServices.service',
            'orders.customer',
            'orders.shopService.service',
        ]);

        $orders = $shop->orders->sortByDesc('id')->values();

        return view('shops.show', [
            'shop' => $shop,
            'organization' => $shop->organization,
            'currentRole' => $currentRole,
            'serviceCount' => $shop->shopServices->count(),
            'orderCount' => $orders->count(),
            'completedOrderCount' => $orders->where('status', 'completed')->count(),
            'totalRevenue' => $orders->sum('total_price'),
            'unpaidBalance' => $orders->where('payment_status', 'unpaid')->sum('total_price'),
            'recentOrders' => $orders->take(5),
        ]);
    }

    public function create(Request $request): View
    {
        $organization = $this->currentOrganization($request);

        if (! $organization) {
            return view('shops.create', ['organization' => null]);
        }

        Gate::authorize('create', [Shop::class, $organization]);

        return view('shops.create', ['organization' => $organization]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'shop_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Gate::authorize('create', [Shop::class, $this->currentOrganization($request)]);

        Shop::create($validated);

        return redirect()->route('dashboard')->with('success', 'Shop created!');
    }

    public function edit(Request $request, Shop $shop): View
    {
        Gate::authorize('update', $shop);

        return view('shops.edit', ['shop' => $shop]);
    }

    public function update(Request $request, Shop $shop): RedirectResponse
    {
        Gate::authorize('update', $shop);

        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $shop->update($validated);

        return redirect()->route('dashboard')->with('success', 'Shop updated!');
    }

    public function destroy(Request $request, Shop $shop): RedirectResponse
    {
        Gate::authorize('delete', $shop);

        $shop->delete();

        return redirect()->route('dashboard')->with('success', 'Shop deleted!');
    }
}
