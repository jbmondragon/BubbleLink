<?php

/**
 * Order Controller (Business Owner Side)
 *
 * Manages all order-related operations for shop owners, including:
 * order listing, creation, and status/payment updates.
 *
 * Responsibilities:
 *
 * 1. Order Listing (index):
 *    - Retrieves all shops owned by the authenticated user
 *    - Applies multiple optional filters:
 *        - Shop selection
 *        - Order status
 *        - Payment status
 *        - Date range (from/to)
 *    - Loads orders with related customer and service data
 *    - Computes aggregated metrics:
 *        - Total displayed orders
 *        - Pending and completed counts
 *        - Revenue breakdown (total, paid, unpaid)
 *    - Filters out empty shops when filters are applied
 *
 * 2. Order Creation (store):
 *    - Validates customer and order input data
 *    - Ensures shop ownership before allowing order creation
 *    - Validates shop-service relationship integrity
 *    - Creates or reuses customer user accounts based on email
 *    - Handles conditional fields based on service mode (walk-in or delivery)
 *    - Initializes order with default status and payment state
 *
 * 3. Order Updates (update):
 *    - Authorizes updates via policy (Gate)
 *    - Validates allowed status transitions and payment updates
 *    - Ensures request integrity by matching order ID
 *    - Updates order status, payment status, and weight when applicable
 *
 * 4. Query Filtering (applyOrderFilters):
 *    - Centralized filtering logic for:
 *        - Order status
 *        - Payment status
 *        - Date range constraints
 *
 * Security & Integrity:
 * - Enforces ownership-based access control for shop and order operations
 * - Uses Laravel Gate policies for authorization
 * - Prevents cross-shop service assignment validation bypass
 * - Ensures strict validation of all incoming request data
 *
 * Design Notes:
 * - Combines business logic with filtering for a unified owner dashboard experience
 * - Uses eager loading to optimize performance and reduce query overhead
 * - Aggregates financial and operational metrics for reporting
 */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $shops = $this->ownerShops($request)->get();

        if ($shops->isEmpty()) {
            return redirect()
                ->route('shops.create')
                ->with('warning', 'Create your first shop before managing orders.');
        }

        $selectedShopId = trim((string) $request->string('shop_id'));
        $statusFilter = (string) $request->string('status');
        $paymentStatusFilter = (string) $request->string('payment_status');
        $fromDate = trim((string) $request->string('from_date'));
        $toDate = trim((string) $request->string('to_date'));

        if ($selectedShopId !== '' && ! ctype_digit($selectedShopId)) {
            $selectedShopId = '';
        }

        if (! in_array($statusFilter, ['', 'pending', 'accepted', 'awaiting_dropoff', 'rejected', 'in_progress', 'completed'], true)) {
            $statusFilter = '';
        }

        if (! in_array($paymentStatusFilter, ['', 'paid', 'unpaid'], true)) {
            $paymentStatusFilter = '';
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
            $fromDate = '';
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate)) {
            $toDate = '';
        }

        $shopIds = $shops->pluck('id');

        $orderShops = Shop::whereIn('id', $shopIds)
            ->when($selectedShopId !== '', fn ($query) => $query->whereKey($selectedShopId))
            ->with('shopServices.service')
            ->with([
                'orders' => fn ($query) => $this->applyOrderFilters($query, $statusFilter, $paymentStatusFilter, $fromDate, $toDate)
                    ->with(['customer', 'shopService.service'])
                    ->latest('id'),
            ])
            ->orderBy('shop_name')
            ->get();

        if ($selectedShopId === '' && ($statusFilter !== '' || $paymentStatusFilter !== '' || $fromDate !== '' || $toDate !== '')) {
            $orderShops = $orderShops->filter(fn ($shop) => $shop->orders->isNotEmpty())->values();
        }

        $displayedOrders = $orderShops->flatMap->orders;

        return view('orders.index', [
            'shops' => $shops,
            'orderShops' => $orderShops,
            'selectedShopId' => $selectedShopId,
            'statusFilter' => $statusFilter,
            'paymentStatusFilter' => $paymentStatusFilter,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'displayedOrderCount' => $displayedOrders->count(),
            'pendingOrderCount' => $displayedOrders->where('status', 'pending')->count(),
            'completedOrderCount' => $displayedOrders->where('status', 'completed')->count(),
            'displayedRevenue' => $displayedOrders->sum('total_price'),
            'paidRevenue' => $displayedOrders->where('payment_status', 'paid')->sum('total_price'),
            'unpaidBalance' => $displayedOrders->where('payment_status', 'unpaid')->sum('total_price'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('orderCreate', [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_contact_number' => 'nullable|string|max:255',
            'shop_id' => [
                'required',
                Rule::exists('shops', 'id')->where(fn ($query) => $query->where('owner_user_id', $request->user()->id)),
            ],
            'shop_service_id' => [
                'required',
                Rule::exists('shop_services', 'id')->where(fn ($query) => $query->where('shop_id', $request->integer('shop_id'))),
            ],
            'service_mode' => 'required|in:walk_in,delivery_only',
            'delivery_address' => 'nullable|required_if:service_mode,delivery_only|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'delivery_datetime' => 'nullable|required_if:service_mode,delivery_only|date',
            'payment_method' => 'nullable|in:gcash,cash',
            'payment_status' => 'nullable|in:paid,unpaid',
        ]);

        $requiresDelivery = $validated['service_mode'] === 'delivery_only';

        $shop = Shop::query()->findOrFail($validated['shop_id']);
        $shopService = ShopService::query()->with('shop')->findOrFail($validated['shop_service_id']);

        Gate::authorize('create', [Order::class, $shop]);

        abort_unless($shopService->shop_id === $shop->id, 422);

        $customer = User::query()->firstOrCreate(
            ['email' => $validated['customer_email']],
            [
                'name' => $validated['customer_name'],
                'password' => Str::password(32),
                'contact_number' => $validated['customer_contact_number'],
            ]
        );

        if (! $customer->contact_number && filled($validated['customer_contact_number'])) {
            $customer->update(['contact_number' => $validated['customer_contact_number']]);
        }

        Order::create([
            'customer_id' => $customer->id,
            'shop_id' => $shop->id,
            'shop_service_id' => $shopService->id,
            'service_mode' => $validated['service_mode'],
            'pickup_address' => null,
            'delivery_address' => $requiresDelivery ? ($validated['delivery_address'] ?? null) : null,
            'weight' => $validated['weight'] ?? null,
            'pickup_datetime' => null,
            'delivery_datetime' => $requiresDelivery ? ($validated['delivery_datetime'] ?? null) : null,
            'total_price' => $shopService->price,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_status' => $validated['payment_status'] ?? 'unpaid',
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created!');
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('update', $order);

        $validated = $request->validateWithBag('orderUpdate-'.$order->id, [
            'order_id' => 'required|integer|in:'.$order->id,
            'status' => 'required|in:pending,accepted,awaiting_dropoff,rejected,in_progress,completed',
            'payment_status' => 'nullable|in:paid,unpaid',
            'weight' => 'nullable|numeric|min:0',
        ]);

        unset($validated['order_id']);

        $order->update($validated);

        return redirect()->route('orders.index')->with('success', 'Order updated!');
    }

    private function applyOrderFilters($query, string $statusFilter, string $paymentStatusFilter, string $fromDate, string $toDate)
    {
        return $query
            ->when($statusFilter !== '', fn ($builder) => $builder->where('status', $statusFilter))
            ->when($paymentStatusFilter !== '', fn ($builder) => $builder->where('payment_status', $paymentStatusFilter))
            ->when($fromDate !== '', fn ($builder) => $builder->whereDate('created_at', '>=', $fromDate))
            ->when($toDate !== '', fn ($builder) => $builder->whereDate('created_at', '<=', $toDate));
    }
}
