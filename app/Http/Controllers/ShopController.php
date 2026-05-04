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

        $shop->load([
            'shopServices.service',
            'orders.customer',
            'orders.shopService.service',
        ]);

        $orders = $shop->orders->sortByDesc('id')->values();

        return view('shops.show', [
            'shop' => $shop,
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
        abort_unless($request->user()->isApprovedShopOwnerRegistration(), 403);

        if ($request->user()->shops()->exists()) {
            abort(403);
        }

        return view('shops.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isApprovedShopOwnerRegistration(), 403);

        if ($request->user()->shops()->exists()) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Shop details are already set. Use Services and Orders to manage day-to-day work.');
        }

        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Shop::create([
            ...$validated,
            'owner_user_id' => $request->user()->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Shop created!');
    }

    public function edit(Request $request, Shop $shop): View
    {
        abort(403);
    }

    public function update(Request $request, Shop $shop): RedirectResponse
    {
        abort(403);
    }

    public function destroy(Request $request, Shop $shop): RedirectResponse
    {
        abort(403);
    }
}
