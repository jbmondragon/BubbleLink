<x-app-layout>
    <div class="owner-page">
        <div class="owner-page-container">
        <!-- Orders header introduces the main operational queue for business users. -->
        <div class="owner-page-header">
            <div>
                <div class="owner-eyebrow">Management</div>
                <h1 class="owner-page-title">Orders</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="owner-back-link">Back to Dashboard</a>
        </div>

        <x-management-nav />

        @if (session('success'))
            <div class="owner-alert owner-alert--success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Summary cards reflect only the currently displayed result set. -->
        <div class="owner-stat-grid owner-stat-grid--orders">
            <div class="owner-stat-card">
                <div class="owner-stat-label">Displayed Orders</div>
                <div class="owner-stat-value">{{ $displayedOrderCount }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Pending</div>
                <div class="owner-stat-value owner-stat-value--amber">{{ $pendingOrderCount }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Completed</div>
                <div class="owner-stat-value owner-stat-value--success">{{ $completedOrderCount }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Displayed Revenue</div>
                <div class="owner-stat-value owner-stat-value--sky">₱{{ number_format((float) $displayedRevenue, 2) }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Paid Revenue</div>
                <div class="owner-stat-value owner-stat-value--indigo">₱{{ number_format((float) $paidRevenue, 2) }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Unpaid Balance</div>
                <div class="owner-stat-value owner-stat-value--rose">₱{{ number_format((float) $unpaidBalance, 2) }}</div>
            </div>
        </div>

        <!-- Filter form narrows the visible queue before the owner edits individual orders. -->
        <div class="owner-panel">
            <div class="mb-4">
                <h2 class="owner-section-title">Filter Orders</h2>
                <p class="owner-section-copy">Filter the orders list by shop, status, and payment status.</p>
            </div>

            <form method="GET" action="{{ route('orders.index') }}" class="owner-form-grid owner-form-grid--filter">
                <div>
                    <x-input-label for="order_filter_shop_id" value="Shop" />
                    <select id="order_filter_shop_id" name="shop_id" class="owner-form-control">
                        <option value="">All Shops</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" @selected($selectedShopId === (string) $shop->id)>{{ $shop->shop_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="order_filter_status" value="Status" />
                    <select id="order_filter_status" name="status" class="owner-form-control">
                        <option value="" @selected($statusFilter === '')>All Statuses</option>
                        @foreach(['pending', 'accepted', 'awaiting_dropoff', 'rejected', 'in_progress', 'completed'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($statusFilter === $statusOption)>{{ str($statusOption)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="order_filter_payment_status" value="Payment Status" />
                    <select id="order_filter_payment_status" name="payment_status" class="owner-form-control">
                        <option value="" @selected($paymentStatusFilter === '')>All Payments</option>
                        @foreach(['unpaid', 'paid'] as $paymentStatus)
                            <option value="{{ $paymentStatus }}" @selected($paymentStatusFilter === $paymentStatus)>{{ ucfirst($paymentStatus) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="order_filter_from_date" value="From Date" />
                    <x-text-input id="order_filter_from_date" name="from_date" type="date" class="mt-1 w-full" :value="$fromDate" />
                </div>
                <div>
                    <x-input-label for="order_filter_to_date" value="To Date" />
                    <x-text-input id="order_filter_to_date" name="to_date" type="date" class="mt-1 w-full" :value="$toDate" />
                </div>
                <div class="owner-form-actions">
                    <x-primary-button>Apply</x-primary-button>
                    @if ($selectedShopId !== '' || $statusFilter !== '' || $paymentStatusFilter !== '' || $fromDate !== '' || $toDate !== '')
                        <a href="{{ route('orders.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        @php($shopServiceOptions = $shops->flatMap(fn ($shop) => $shop->shopServices->map(fn ($shopService) => [
            'id' => $shopService->id,
            'shop_id' => $shop->id,
            'label' => $shopService->service->name.' - ₱'.number_format((float) $shopService->price, 2),
        ]))->values())

        <!-- Inline create-order form lets staff add orders without leaving the queue screen. -->
        <div
            class="owner-panel"
            x-data="ownerOrderForm({
                selectedShopId: @js((string) old('shop_id')),
                selectedShopServiceId: @js((string) old('shop_service_id')),
                serviceMode: @js(old('service_mode', 'walk_in')),
                shopServiceOptions: @js($shopServiceOptions),
            })"
        >
            @php($orderCreateErrors = $errors->getBag('orderCreate'))
            <div class="owner-panel-header">
                <div>
                    <h2 class="owner-section-title">Create Order</h2>
                    <p class="owner-section-copy">Create a new customer order and assign it to a shop service.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('orders.store') }}" class="owner-form-grid">
                @csrf
                <div>
                    <x-input-label for="customer_name" value="Customer Name" />
                    <x-text-input id="customer_name" name="customer_name" class="mt-1 w-full" :value="old('customer_name')" required />
                    <x-input-error :messages="$orderCreateErrors->get('customer_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="customer_email" value="Customer Email" />
                    <x-text-input id="customer_email" name="customer_email" type="email" class="mt-1 w-full" :value="old('customer_email')" required />
                    <x-input-error :messages="$orderCreateErrors->get('customer_email')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="customer_contact_number" value="Customer Contact Number" />
                    <x-text-input id="customer_contact_number" name="customer_contact_number" class="mt-1 w-full" :value="old('customer_contact_number')" />
                    <x-input-error :messages="$orderCreateErrors->get('customer_contact_number')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="shop_id" value="Shop" />
                    <select id="shop_id" name="shop_id" x-model="selectedShopId" @change="syncSelectedShopService()" class="owner-form-control" required>
                        <option value="">Select a shop</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" @selected((string) old('shop_id') === (string) $shop->id)>{{ $shop->shop_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('shop_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="shop_service_id" value="Shop Service" />
                    <select id="shop_service_id" name="shop_service_id" x-model="selectedShopServiceId" class="owner-form-control" required>
                        <option value="">Select a shop service</option>
                        <template x-for="shopService in filteredShopServices()" :key="shopService.id">
                            <option :value="shopService.id" x-text="shopService.label"></option>
                        </template>
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('shop_service_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="service_mode" value="Service Mode" />
                    <select id="service_mode" name="service_mode" x-model="serviceMode" class="owner-form-control" required>
                        <option value="walk_in" @selected(old('service_mode', 'walk_in') === 'walk_in')>Walk-in Drop-off and Pick-up</option>
                        <option value="delivery_only" @selected(old('service_mode') === 'delivery_only')>Delivery Only</option>
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('service_mode')" class="mt-2" />
                </div>
                <div x-show="needsDelivery()" x-cloak>
                    <x-input-label for="delivery_address" value="Delivery Address" />
                    <x-text-input id="delivery_address" name="delivery_address" x-bind:disabled="! needsDelivery()" class="mt-1 w-full" :value="old('delivery_address')" />
                    <x-input-error :messages="$orderCreateErrors->get('delivery_address')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="weight" value="Weight (kg)" />
                    <x-text-input id="weight" name="weight" type="number" step="0.01" min="0" class="mt-1 w-full" :value="old('weight')" />
                    <x-input-error :messages="$orderCreateErrors->get('weight')" class="mt-2" />
                </div>
                <div x-show="needsDelivery()" x-cloak>
                    <x-input-label for="delivery_datetime" value="Delivery Schedule" />
                    <x-text-input id="delivery_datetime" name="delivery_datetime" type="datetime-local" x-bind:disabled="! needsDelivery()" class="mt-1 w-full" :value="old('delivery_datetime')" />
                    <x-input-error :messages="$orderCreateErrors->get('delivery_datetime')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="payment_method" value="Payment Method" />
                    <select id="payment_method" name="payment_method" class="owner-form-control">
                        <option value="">Select a payment method</option>
                        <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
                        <option value="gcash" @selected(old('payment_method') === 'gcash')>GCash</option>
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('payment_method')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="payment_status" value="Payment Status" />
                    <select id="payment_status" name="payment_status" class="owner-form-control">
                        <option value="unpaid" @selected(old('payment_status', 'unpaid') === 'unpaid')>Unpaid</option>
                        <option value="paid" @selected(old('payment_status') === 'paid')>Paid</option>
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('payment_status')" class="mt-2" />
                </div>
                <div class="md:col-span-2 xl:col-span-3">
                    <x-primary-button :disabled="$shops->isEmpty() || $shops->every(fn ($shop) => $shop->shopServices->isEmpty())">Create Order</x-primary-button>
                </div>
            </form>
        </div>

        <!-- Per-shop order tables expose editable status, weight, and payment controls. -->
        <div class="owner-stack">
            @forelse($orderShops as $shop)
                <div class="owner-panel">
                    <div class="owner-panel-header">
                        <div>
                            <h2 class="owner-section-title">{{ $shop->shop_name }}</h2>
                            <p class="owner-section-copy">{{ $shop->orders->count() }} orders</p>
                        </div>
                    </div>

                    <div class="owner-table-wrap">
                        <table class="owner-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Mode</th>
                                    <th>Weight</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Schedule</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shop->orders as $order)
                                    @php($orderErrors = $errors->getBag('orderUpdate-'.$order->id))
                                    @php($failedOrderId = old('order_id'))
                                    <tr>
                                        <td>
                                            <div class="owner-row-title">{{ $order->customer?->name ?? 'Unknown customer' }}</div>
                                            <div class="owner-row-meta">Order #{{ $order->id }}</div>
                                        </td>
                                        <td>{{ $order->shopService?->service?->name ?? 'Unassigned service' }}</td>
                                        <td>{{ str($order->service_mode ?? 'n/a')->replace('_', ' ')->title() }}</td>
                                        <td>
                                            <div class="flex flex-col gap-2">
                                                <input name="weight" form="order-update-{{ $order->id }}" type="number" step="0.01" min="0" value="{{ $failedOrderId == $order->id ? old('weight', $order->weight) : $order->weight }}" class="owner-weight-field">
                                                <x-input-error :messages="$orderErrors->get('weight')" class="mt-0" />
                                            </div>
                                        </td>
                                        <td>₱{{ number_format((float) $order->total_price, 2) }}</td>
                                        <td>
                                            <select name="status" form="order-update-{{ $order->id }}" class="owner-select-inline">
                                                @foreach(['pending', 'accepted', 'awaiting_dropoff', 'rejected', 'in_progress', 'completed'] as $statusOption)
                                                    <option value="{{ $statusOption }}" @selected(($failedOrderId == $order->id ? old('status', $order->status) : $order->status) === $statusOption)>
                                                        {{ str($statusOption)->replace('_', ' ')->title() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$orderErrors->get('status')" class="mt-2" />
                                        </td>
                                        <td>
                                            <select name="payment_status" form="order-update-{{ $order->id }}" class="owner-select-inline">
                                                @foreach(['unpaid', 'paid'] as $paymentStatus)
                                                    <option value="{{ $paymentStatus }}" @selected(($failedOrderId == $order->id ? old('payment_status', $order->payment_status ?? 'unpaid') : ($order->payment_status ?? 'unpaid')) === $paymentStatus)>
                                                        {{ ucfirst($paymentStatus) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$orderErrors->get('payment_status')" class="mt-2" />
                                        </td>
                                        <td class="owner-row-meta">
                                            <div>Pickup: {{ $order->pickup_datetime?->format('M d, Y h:i A') ?? 'Not set' }}</div>
                                            <div>Delivery: {{ $order->delivery_datetime?->format('M d, Y h:i A') ?? 'Not set' }}</div>
                                        </td>
                                        <td>
                                            <form id="order-update-{{ $order->id }}" method="POST" action="{{ route('orders.update', $order) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <x-primary-button>Save</x-primary-button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="owner-table-empty">No orders for this shop yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="owner-panel owner-panel--empty">
                    {{ $selectedShopId !== '' || $statusFilter !== '' || $paymentStatusFilter !== '' || $fromDate !== '' || $toDate !== '' ? 'No orders match the current filters.' : 'Create a shop first before managing orders.' }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>