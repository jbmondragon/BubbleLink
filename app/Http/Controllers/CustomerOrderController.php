<?php

namespace App\Http\Controllers;

/**
 * Handles customer-facing order creation, order history, order details, and
 * the remaining legacy rating endpoint.
 */

use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Implements SID4 and SID5 for customer ordering.
 *
 * Responsibilities:
 * - SID4: Place orders with different service options such as pickup, delivery, both, or walk-in
 * - SID5: View order history, inspect order details, and submit a shop rating on completed work
 */
class CustomerOrderController extends Controller
{
    public function create(Request $request, Shop $shop): View //Creates the order form
    {
        $this->ensureCustomer($request); //verifies current user is a customer, 403 if not

        $shop->load(['shopServices.service']); //Loads all the data from shopServices.service

        abort_if($shop->shopServices->isEmpty(), 404); //If no services are found, return 404

        return view('customer.orders.create', [ //passes to create.blade.php within views/customers/orders to create the order form
            'shop' => $shop,
            'services' => $shop->shopServices->sortBy(fn ($shopService) => $shopService->service->name)->values(),
        ]);
    }

    public function store(Request $request, Shop $shop): RedirectResponse //Stores the order
    {
        $this->ensureCustomer($request); //verifies current user is a customer, 403 if not

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
            'number_of_loads' => null,
            'pickup_datetime' => $requiresPickup ? ($validated['pickup_datetime'] ?? null) : null,
            'delivery_datetime' => $requiresDelivery ? ($validated['delivery_datetime'] ?? null) : null,
            'total_price' => $shopService->price * ($validated['number_of_loads'] ?? 1),
            'status' => 'pending',
            'payment_method' => null,
            'payment_status' => 'unpaid',
        ]);

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', 'Order placed successfully.');
    }

    public function index(Request $request): View //Indexes current user order into views/customer/orders/index.blade.php
    {
        $this->ensureCustomer($request); // verifies current user is a customer, 403 if not

        $orders = $request->user()
            ->orders()
            ->with(['shop', 'shopService.service'])
            ->latest('id')
            ->get();

        return view('customer.orders.index', [
            'orders' => $orders,
            'totalOrderCount' => $orders->count(),
            'pendingOrderCount' => $orders->where('status', 'pending')->count(),
            'completedOrderCount' => $orders->where('status', 'completed')->count(),
        ]);
    }

    public function rate(Request $request, Order $order): RedirectResponse
    {
        $this->ensureCustomer($request); // verifies current user is a customer, 403 if not
        Gate::authorize('rate', $order); // verifies current user is the owner of the order, 403 if not

        $validated = $request->validateWithBag('customerOrderRating', [ //validation with error bag for rating, required, must be an ingteger, between 1 to 5
            'shop_rating' => 'required|integer|between:1,5',
        ]);

        $order->update([ // updates the shop rating and rated_at timestamp
            'shop_rating' => $validated['shop_rating'], // updates the shop rating
            'rated_at' => now(), //timestamp for audit trail
        ]);

        return redirect()
            ->route('customer.orders.show', $order) // redirects to the order show page
            ->with('success', 'Thanks for rating this shop.'); // success message
    }

    public function show(Request $request, Order $order): View
    {
        $this->ensureCustomer($request); // verifies current user is a customer, 403 if not
        Gate::authorize('view', $order); // verifies current user is the owner of the order, 403 if not

        $order->load(['shop', 'shopService.service']); // loads the shop and shop service with the service

        return view('customer.orders.show', [ // loads the order show page
            'order' => $order,
        ]);
    }
}
