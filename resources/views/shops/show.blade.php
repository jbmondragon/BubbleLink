<x-app-layout>
    <div class="owner-page">
        <div class="owner-page-container">
        <!-- Shop workspace header anchors the metrics and tables to one branch. -->
        <div class="owner-page-header">
            <div>
                <div class="owner-eyebrow">Shop Workspace</div>
                <h1 class="owner-page-title">{{ $shop->shop_name }}</h1>
                <p class="owner-page-copy">{{ $shop->address }}{{ $shop->contact_number ? ' • '.$shop->contact_number : '' }}</p>
            </div>
            <div class="owner-form-actions">
                <a href="{{ route('dashboard') }}" class="owner-back-link">Back to Dashboard</a>
            </div>
        </div>

        <x-management-nav />

        <!-- Summary cards highlight the shop's operational totals. -->
        <div class="owner-stat-grid owner-stat-grid--wide">
            <div class="owner-stat-card">
                <div class="owner-stat-label">Services</div>
                <div class="owner-stat-value">{{ $serviceCount }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Orders</div>
                <div class="owner-stat-value">{{ $orderCount }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Completed</div>
                <div class="owner-stat-value owner-stat-value--success">{{ $completedOrderCount }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Revenue</div>
                <div class="owner-stat-value owner-stat-value--sky">₱{{ number_format((float) $totalRevenue, 2) }}</div>
            </div>
            <div class="owner-stat-card">
                <div class="owner-stat-label">Unpaid Balance</div>
                <div class="owner-stat-value owner-stat-value--rose">₱{{ number_format((float) $unpaidBalance, 2) }}</div>
            </div>
        </div>

        <!-- Side-by-side tables show assigned services and the latest shop orders. -->
        <div class="owner-grid-wide">
            <div class="owner-panel">
                <div class="owner-panel-header">
                    <h2 class="owner-section-title">Assigned Services</h2>
                    <a href="{{ route('services.index') }}" class="owner-muted-link">Manage Services</a>
                </div>

                <div class="owner-table-shell">
                    <table class="owner-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shop->shopServices as $shopService)
                                <tr>
                                    <td>{{ $shopService->service->name }}</td>
                                    <td>₱{{ number_format((float) $shopService->price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="owner-table-empty">No services assigned yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="owner-panel">
                <div class="owner-panel-header">
                    <h2 class="owner-section-title">Recent Orders</h2>
                    <a href="{{ route('orders.index', ['shop_id' => $shop->id]) }}" class="owner-muted-link">Open Orders Page</a>
                </div>

                <div class="owner-table-shell">
                    <table class="owner-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <div class="owner-row-title">{{ $order->customer?->name ?? 'Unknown customer' }}</div>
                                        <div class="owner-row-meta">Order #{{ $order->id }}</div>
                                    </td>
                                    <td>{{ $order->shopService?->service?->name ?? 'Unassigned service' }}</td>
                                    <td>{{ str($order->status)->replace('_', ' ')->title() }}</td>
                                    <td>₱{{ number_format((float) $order->total_price, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="owner-table-empty">No orders yet for this shop.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Static shop profile details remain available below the operational tables. -->
        <div class="owner-panel">
            <h2 class="owner-section-title">Shop Details</h2>
            <dl class="owner-details-grid mt-4">
                <div>
                    <dt class="owner-detail-label">Address</dt>
                    <dd class="owner-detail-value">{{ $shop->address }}</dd>
                </div>
                <div>
                    <dt class="owner-detail-label">Contact Number</dt>
                    <dd class="owner-detail-value">{{ $shop->contact_number ?: 'No contact number' }}</dd>
                </div>
                <div>
                    <dt class="owner-detail-label">Description</dt>
                    <dd class="owner-detail-value">{{ $shop->description ?: 'No description provided' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-app-layout>