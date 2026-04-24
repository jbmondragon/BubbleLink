<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function create(Request $request, Shop $shop): View
    {
        $this->ensureCustomer($request);

        $shop->load(['organization', 'shopServices.service']);

        abort_if($shop->shopServices->isEmpty(), 404);

        return view('customer.orders.create', [
            'shop' => $shop,
            'services' => $shop->shopServices->sortBy(fn ($shopService) => $shopService->service->name)->values(),
        ]);
    }

    public function store(Request $request, Shop $shop): RedirectResponse
    {
        $this->ensureCustomer($request);

        $validated = $request->validateWithBag('customerOrderCreate', [
            'shop_service_id' => [
                'required',
                Rule::exists('shop_services', 'id')->where(fn ($query) => $query->where('shop_id', $shop->id)),
            ],
            'service_mode' => 'required|in:pickup_only,delivery_only,both,walk_in',
            'pickup_address' => 'nullable|required_if:service_mode,pickup_only,both|string|max:255',
            'delivery_address' => 'nullable|required_if:service_mode,delivery_only,both|string|max:255',
            'pickup_datetime' => 'nullable|required_if:service_mode,pickup_only,both|date',
            'delivery_datetime' => 'nullable|required_if:service_mode,delivery_only,both|date',
        ]);

        $requiresPickup = in_array($validated['service_mode'], ['pickup_only', 'both'], true);
        $requiresDelivery = in_array($validated['service_mode'], ['delivery_only', 'both'], true);

        $shopService = ShopService::query()->with('service')->findOrFail($validated['shop_service_id']);

        abort_unless($shopService->shop_id === $shop->id, 422);

        $order = Order::create([
            'customer_id' => $request->user()->id,
            'shop_id' => $shop->id,
            'shop_service_id' => $shopService->id,
            'service_mode' => $validated['service_mode'],
            'pickup_address' => $requiresPickup ? ($validated['pickup_address'] ?? null) : null,
            'delivery_address' => $requiresDelivery ? ($validated['delivery_address'] ?? null) : null,
            'weight' => null,
            'pickup_datetime' => $requiresPickup ? ($validated['pickup_datetime'] ?? null) : null,
            'delivery_datetime' => $requiresDelivery ? ($validated['delivery_datetime'] ?? null) : null,
            'total_price' => $shopService->price,
            'status' => 'pending',
            'payment_method' => null,
            'payment_status' => 'unpaid',
        ]);

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Order placed successfully.');
    }

    public function index(Request $request): View
    {
        $this->ensureCustomer($request);

        $orders = $request->user()
            ->orders()
            ->with(['shop.organization', 'shopService.service'])
            ->latest('id')
            ->get();

        return view('customer.orders.index', [
            'orders' => $orders,
        ]);
    }

    public function rate(Request $request, Order $order): RedirectResponse
    {
        $this->ensureCustomer($request);
        Gate::authorize('rate', $order);

        $validated = $request->validateWithBag('customerOrderRating', [
            'shop_rating' => 'required|integer|between:1,5',
        ]);

        $order->update([
            'shop_rating' => $validated['shop_rating'],
            'rated_at' => now(),
        ]);

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Thanks for rating this shop.');
    }

    public function show(Request $request, Order $order): View
    {
        $this->ensureCustomer($request);
        Gate::authorize('view', $order);

        $order->load(['shop.organization', 'shopService.service']);

        return view('customer.orders.show', [
            'order' => $order,
        ]);
    }
}
