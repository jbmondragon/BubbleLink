<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="mb-8 flex items-center justify-between gap-4">
            <div>
                <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Management</div>
                <h1 class="text-3xl font-bold">Orders</h1>
                <p class="mt-2 text-sm text-slate-600">Review customer orders by shop and update status or payment state.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-600 hover:underline">Back to Dashboard</a>
        </div>

        <x-management-nav :organization="$organization" :current-role="$currentRole" />

        @if ($currentMembership?->shop)
            <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 px-5 py-4 text-sky-950">
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">Assigned Shop</div>
                <div class="mt-2 text-lg font-semibold">{{ $currentMembership->shop->shop_name }}</div>
                <p class="mt-1 text-sm text-sky-900/75">Order management on this page is currently scoped to your assigned shop.</p>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Displayed Orders</div>
                <div class="mt-2 text-2xl font-bold text-slate-900">{{ $displayedOrderCount }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pending</div>
                <div class="mt-2 text-2xl font-bold text-amber-600">{{ $pendingOrderCount }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Completed</div>
                <div class="mt-2 text-2xl font-bold text-emerald-600">{{ $completedOrderCount }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Displayed Revenue</div>
                <div class="mt-2 text-2xl font-bold text-sky-600">₱{{ number_format((float) $displayedRevenue, 2) }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Paid Revenue</div>
                <div class="mt-2 text-2xl font-bold text-indigo-600">₱{{ number_format((float) $paidRevenue, 2) }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Unpaid Balance</div>
                <div class="mt-2 text-2xl font-bold text-rose-600">₱{{ number_format((float) $unpaidBalance, 2) }}</div>
            </div>
        </div>

        <div class="mb-6 bg-white shadow rounded-lg p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold">Filter Orders</h2>
                <p class="mt-1 text-sm text-slate-600">Filter the orders list by shop, status, and payment status.</p>
            </div>

            <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-[minmax(0,1.2fr),minmax(0,1fr),minmax(0,1fr),minmax(0,1fr),minmax(0,1fr),auto] md:items-end">
                <div>
                    <x-input-label for="order_filter_shop_id" value="Shop" />
                    <select id="order_filter_shop_id" name="shop_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Shops</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" @selected($selectedShopId === (string) $shop->id)>{{ $shop->shop_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="order_filter_status" value="Status" />
                    <select id="order_filter_status" name="status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="" @selected($statusFilter === '')>All Statuses</option>
                        @foreach(['pending', 'accepted', 'awaiting_dropoff', 'rejected', 'in_progress', 'completed'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($statusFilter === $statusOption)>{{ str($statusOption)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="order_filter_payment_status" value="Payment Status" />
                    <select id="order_filter_payment_status" name="payment_status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
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
                <div class="flex items-center gap-3">
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

        <div
            class="mb-6 bg-white shadow rounded-lg p-6"
            x-data="{
                selectedShopId: @js((string) old('shop_id')),
                selectedShopServiceId: @js((string) old('shop_service_id')),
                shopServiceOptions: @js($shopServiceOptions),
                filteredShopServices() {
                    return this.shopServiceOptions.filter((option) => this.selectedShopId === '' || String(option.shop_id) === String(this.selectedShopId));
                },
            }"
        >
            @php($orderCreateErrors = $errors->getBag('orderCreate'))
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Create Order</h2>
                    <p class="mt-1 text-sm text-slate-600">Create a new customer order and assign it to a shop service.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('orders.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
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
                    <select id="shop_id" name="shop_id" x-model="selectedShopId" @change="if (! filteredShopServices().some((option) => String(option.id) === String(selectedShopServiceId))) { selectedShopServiceId = ''; }" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Select a shop</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" @selected((string) old('shop_id') === (string) $shop->id)>{{ $shop->shop_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('shop_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="shop_service_id" value="Shop Service" />
                    <select id="shop_service_id" name="shop_service_id" x-model="selectedShopServiceId" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">Select a shop service</option>
                        <template x-for="shopService in filteredShopServices()" :key="shopService.id">
                            <option :value="shopService.id" x-text="shopService.label"></option>
                        </template>
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('shop_service_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="service_mode" value="Service Mode" />
                    <select id="service_mode" name="service_mode" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="pickup_only" @selected(old('service_mode') === 'pickup_only')>Pickup Only</option>
                        <option value="delivery_only" @selected(old('service_mode') === 'delivery_only')>Delivery Only</option>
                        <option value="both" @selected(old('service_mode') === 'both')>Pickup and Delivery</option>
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('service_mode')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="pickup_address" value="Pickup Address" />
                    <x-text-input id="pickup_address" name="pickup_address" class="mt-1 w-full" :value="old('pickup_address')" />
                    <x-input-error :messages="$orderCreateErrors->get('pickup_address')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="delivery_address" value="Delivery Address" />
                    <x-text-input id="delivery_address" name="delivery_address" class="mt-1 w-full" :value="old('delivery_address')" />
                    <x-input-error :messages="$orderCreateErrors->get('delivery_address')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="weight" value="Weight (kg)" />
                    <x-text-input id="weight" name="weight" type="number" step="0.01" min="0" class="mt-1 w-full" :value="old('weight')" />
                    <x-input-error :messages="$orderCreateErrors->get('weight')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="pickup_datetime" value="Pickup Schedule" />
                    <x-text-input id="pickup_datetime" name="pickup_datetime" type="datetime-local" class="mt-1 w-full" :value="old('pickup_datetime')" />
                    <x-input-error :messages="$orderCreateErrors->get('pickup_datetime')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="delivery_datetime" value="Delivery Schedule" />
                    <x-text-input id="delivery_datetime" name="delivery_datetime" type="datetime-local" class="mt-1 w-full" :value="old('delivery_datetime')" />
                    <x-input-error :messages="$orderCreateErrors->get('delivery_datetime')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="payment_method" value="Payment Method" />
                    <select id="payment_method" name="payment_method" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Select a payment method</option>
                        <option value="cash" @selected(old('payment_method') === 'cash')>Cash</option>
                        <option value="gcash" @selected(old('payment_method') === 'gcash')>GCash</option>
                    </select>
                    <x-input-error :messages="$orderCreateErrors->get('payment_method')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="payment_status" value="Payment Status" />
                    <select id="payment_status" name="payment_status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
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

        <div class="space-y-6">
            @forelse($orderShops as $shop)
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $shop->shop_name }}</h2>
                            <p class="text-sm text-slate-500">{{ $shop->orders->count() }} orders</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Customer</th>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Service</th>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Mode</th>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Amount</th>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Payment</th>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Schedule</th>
                                    <th class="px-4 py-3 text-left font-medium text-slate-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($shop->orders as $order)
                                    @php($orderErrors = $errors->getBag('orderUpdate-'.$order->id))
                                    @php($failedOrderId = old('order_id'))
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-900">{{ $order->customer?->name ?? 'Unknown customer' }}</div>
                                            <div class="text-xs text-slate-500">Order #{{ $order->id }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $order->shopService?->service?->name ?? 'Unassigned service' }}</td>
                                        <td class="px-4 py-3">{{ str($order->service_mode ?? 'n/a')->replace('_', ' ')->title() }}</td>
                                        <td class="px-4 py-3">₱{{ number_format((float) $order->total_price, 2) }}</td>
                                        <td class="px-4 py-3">
                                            <select name="status" form="order-update-{{ $order->id }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                                                @foreach(['pending', 'accepted', 'awaiting_dropoff', 'rejected', 'in_progress', 'completed'] as $statusOption)
                                                    <option value="{{ $statusOption }}" @selected(($failedOrderId == $order->id ? old('status', $order->status) : $order->status) === $statusOption)>
                                                        {{ str($statusOption)->replace('_', ' ')->title() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$orderErrors->get('status')" class="mt-2" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <select name="payment_status" form="order-update-{{ $order->id }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                                                @foreach(['unpaid', 'paid'] as $paymentStatus)
                                                    <option value="{{ $paymentStatus }}" @selected(($failedOrderId == $order->id ? old('payment_status', $order->payment_status ?? 'unpaid') : ($order->payment_status ?? 'unpaid')) === $paymentStatus)>
                                                        {{ ucfirst($paymentStatus) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$orderErrors->get('payment_status')" class="mt-2" />
                                        </td>
                                        <td class="px-4 py-3 text-xs text-slate-500">
                                            <div>Pickup: {{ $order->pickup_datetime?->format('M d, Y h:i A') ?? 'Not set' }}</div>
                                            <div>Delivery: {{ $order->delivery_datetime?->format('M d, Y h:i A') ?? 'Not set' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
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
                                        <td colspan="8" class="px-4 py-4 text-gray-400">No orders for this shop yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow rounded-lg p-6 text-gray-400">
                    {{ $selectedShopId !== '' || $statusFilter !== '' || $paymentStatusFilter !== '' || $fromDate !== '' || $toDate !== '' ? 'No orders match the current filters.' : 'Create a shop first before managing orders.' }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>