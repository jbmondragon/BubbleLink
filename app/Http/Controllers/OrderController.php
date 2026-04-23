<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = $this->currentOrganization($request);
        $membership = $this->currentMembership($request);
        $currentRole = $this->currentRole($request);

        if (! $organization) {
            return redirect()
                ->route('organizations.create')
                ->with('warning', 'Create your organization first to manage orders.');
        }

        if (! in_array($currentRole, ['manager', 'staff'], true)) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Only managers and staff can manage orders. Owners can view order totals from the dashboard.');
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

        $shops = $organization->shops()
            ->when($currentRole !== 'owner', fn ($query) => $query->whereKey($membership?->shop_id ?? 0))
            ->with('shopServices.service')
            ->orderBy('shop_name')
            ->get();

        $orderShops = $organization->shops()
            ->when($currentRole !== 'owner', fn ($query) => $query->whereKey($membership?->shop_id ?? 0))
            ->when($selectedShopId !== '', fn ($query) => $query->whereKey($selectedShopId))
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
            'organization' => $organization,
            'currentMembership' => $membership,
            'currentRole' => $currentRole,
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
        $organization = $this->currentOrganization($request);
        $membership = $this->currentMembership($request);
        $currentRole = $this->currentRole($request);

        if (! $organization) {
            return redirect()
                ->route('organizations.create')
                ->with('warning', 'Create your organization first to manage orders.');
        }

        if (! in_array($currentRole, ['manager', 'staff'], true)) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Only managers and staff can create orders. Owners can view order totals from the dashboard.');
        }

        abort_unless($membership?->shop_id, 403);

        $validated = $request->validateWithBag('orderCreate', [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_contact_number' => 'nullable|string|max:255',
            'shop_id' => 'required|exists:shops,id',
            'shop_service_id' => [
                'required',
                Rule::exists('shop_services', 'id')->where(fn ($query) => $query->where('shop_id', $request->integer('shop_id'))),
            ],
            'service_mode' => 'required|in:pickup_only,delivery_only,both',
            'pickup_address' => 'nullable|required_if:service_mode,pickup_only,both|string|max:255',
            'delivery_address' => 'nullable|required_if:service_mode,delivery_only,both|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'pickup_datetime' => 'nullable|required_if:service_mode,pickup_only,both|date',
            'delivery_datetime' => 'nullable|required_if:service_mode,delivery_only,both|date',
            'payment_method' => 'nullable|in:gcash,cash',
            'payment_status' => 'nullable|in:paid,unpaid',
        ]);

        $shop = Shop::query()->findOrFail($validated['shop_id']);
        $shopService = ShopService::query()->with('shop')->findOrFail($validated['shop_service_id']);

        $this->ensureShopRole($request, $shop, ['manager', 'staff']);

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
            'pickup_address' => $validated['pickup_address'] ?? null,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'pickup_datetime' => $validated['pickup_datetime'] ?? null,
            'delivery_datetime' => $validated['delivery_datetime'] ?? null,
            'total_price' => $shopService->price,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_status' => $validated['payment_status'] ?? 'unpaid',
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created!');
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->ensureOrderRole($request, $order, ['manager', 'staff']);

        $validated = $request->validateWithBag('orderUpdate-'.$order->id, [
            'order_id' => 'required|integer|in:'.$order->id,
            'status' => 'required|in:pending,accepted,awaiting_dropoff,rejected,in_progress,completed',
            'payment_status' => 'nullable|in:paid,unpaid',
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
