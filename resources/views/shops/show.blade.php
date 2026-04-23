<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="mb-8 flex items-center justify-between gap-4">
            <div>
                <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Shop Workspace</div>
                <h1 class="text-3xl font-bold">{{ $shop->shop_name }}</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $shop->address }}{{ $shop->contact_number ? ' • '.$shop->contact_number : '' }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if ($currentRole === 'owner')
                    <a href="{{ route('shops.edit', $shop) }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Edit Shop</a>
                @endif
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-600 hover:underline">Back to Dashboard</a>
            </div>
        </div>

        <x-management-nav :organization="$organization" :current-role="$currentRole" />

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-5">
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Services</div>
                <div class="mt-2 text-2xl font-bold text-slate-900">{{ $serviceCount }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Orders</div>
                <div class="mt-2 text-2xl font-bold text-slate-900">{{ $orderCount }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Completed</div>
                <div class="mt-2 text-2xl font-bold text-emerald-600">{{ $completedOrderCount }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Revenue</div>
                <div class="mt-2 text-2xl font-bold text-sky-600">₱{{ number_format((float) $totalRevenue, 2) }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Unpaid Balance</div>
                <div class="mt-2 text-2xl font-bold text-rose-600">₱{{ number_format((float) $unpaidBalance, 2) }}</div>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-[1fr,1.2fr]">
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold">Assigned Services</h2>
                    @if ($currentRole === 'manager')
                        <a href="{{ route('services.index') }}" class="text-sm text-blue-600 hover:underline">Manage Services</a>
                    @endif
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Service</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($shop->shopServices as $shopService)
                                <tr>
                                    <td class="px-4 py-3">{{ $shopService->service->name }}</td>
                                    <td class="px-4 py-3">₱{{ number_format((float) $shopService->price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-gray-400">No services assigned yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold">Recent Orders</h2>
                    <a href="{{ route('orders.index', ['shop_id' => $shop->id]) }}" class="text-sm text-blue-600 hover:underline">Open Orders Page</a>
                </div>

                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Customer</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Service</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900">{{ $order->customer?->name ?? 'Unknown customer' }}</div>
                                        <div class="text-xs text-slate-500">Order #{{ $order->id }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $order->shopService?->service?->name ?? 'Unassigned service' }}</td>
                                    <td class="px-4 py-3">{{ str($order->status)->replace('_', ' ')->title() }}</td>
                                    <td class="px-4 py-3">₱{{ number_format((float) $order->total_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-gray-400">No orders yet for this shop.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Shop Details</h2>
            <dl class="mt-4 grid grid-cols-1 gap-4 text-sm md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <dt class="font-medium text-slate-500">Organization</dt>
                    <dd class="mt-1 text-slate-900">{{ $organization->name }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-500">Address</dt>
                    <dd class="mt-1 text-slate-900">{{ $shop->address }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-500">Contact Number</dt>
                    <dd class="mt-1 text-slate-900">{{ $shop->contact_number ?: 'No contact number' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-500">Description</dt>
                    <dd class="mt-1 text-slate-900">{{ $shop->description ?: 'No description provided' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-app-layout>