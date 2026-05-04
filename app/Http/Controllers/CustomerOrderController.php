<?php

namespace App\Http\Controllers;

/**
 * Customer Order Controller
 *
 * Handles all customer-side order workflows including:
 * order creation, order listing, order details viewing, and rating submission.
 *
 * Responsibilities:
 *
 * 1. Order Creation (create & store):
 *    - Displays shop-specific service selection form
 *    - Validates service availability within the selected shop
 *    - Applies conditional validation rules based on service mode:
 *        - pickup_only, delivery_only, both, walk_in
 *    - Determines required fields dynamically (pickup/delivery details)
 *    - Creates a new order with default "pending" and "unpaid" status
 *    - Computes total price from selected shop service (server-side trusted source)
 *
 * 2. Order Listing (index):
 *    - Retrieves authenticated customer’s order history
 *    - Eager loads related shop and service data
 *    - Provides summary statistics:
 *        - Total orders
 *        - Pending orders
 *        - Completed orders
 *
 * 3. Order Details (show):
 *    - Displays a single order with full relational context
 *    - Ensures authorization via policy (view access control)
 *    - Loads associated shop and service information
 *
 * 4. Order Rating (rate):
 *    - Allows customers to submit a 1–5 rating for a completed order
 *    - Enforces authorization via policy (rate access control)
 *    - Stores rating value and timestamp for tracking and analytics
 *
 * Security & Integrity:
 * - Ensures only valid customers can access all endpoints via ensureCustomer()
 * - Uses Laravel Gate policies for per-order authorization (view/rate)
 * - Prevents cross-shop service assignment through strict validation checks
 * - Trusts server-side service pricing to prevent client manipulation
 *
 * Design Notes:
 * - Centralizes customer order lifecycle operations in a single controller
 * - Uses eager loading to minimize query overhead
 * - Separates authorization concerns (policies) from business logic
 */

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

        $shop->load(['shopServices.service']);

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

        $order->load(['shop', 'shopService.service']);

        return view('customer.orders.show', [
            'order' => $order,
        ]);
    }
}
