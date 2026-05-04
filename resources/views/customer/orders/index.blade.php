
<!-- This page allows customers to create a laundry order by selecting a service, 
 choosing a service mode (pickup, delivery, walk-in), and providing the necessary address 
 and scheduling details. The form dynamically updates available fields and estimated pricing 
 based on user selections, ensuring only relevant inputs are shown before submitting the order. -->

 <x-app-layout>
    <x-slot name="header">
        <!-- Customer order header keeps the history view -->
        <div class="customer-page-header customer-page-header--split">
            <div>
                <p class="customer-eyebrow customer-eyebrow--blue">Customer Orders</p>
                <h1 class="customer-page-title">My Orders</h1>
            </div>
            <a href="{{ route('customer.shops.index') }}" class="customer-button customer-button--outline">
                Book another service
            </a>
        </div>
    </x-slot>

    <div class="customer-page">
        <div class="customer-page-container">
            <!-- Summary cards give a quick count of order volume and completion state. -->
            <div class="customer-grid-three">
                <div class="customer-stat-card customer-stat-card--dark">
                    <p class="customer-eyebrow text-neutral-200">Total orders</p>
                    <p class="customer-stat-value">{{ $totalOrderCount }}</p>
                </div>
                <div class="customer-stat-card customer-stat-card--light">
                    <p class="customer-eyebrow customer-eyebrow--muted">Pending</p>
                    <p class="customer-stat-value text-slate-900">{{ $pendingOrderCount }}</p>
                </div>
                <div class="customer-stat-card customer-stat-card--light">
                    <p class="customer-eyebrow customer-eyebrow--muted">Completed</p>
                    <p class="customer-stat-value text-slate-900">{{ $completedOrderCount }}</p>
                </div>
            </div>

            <!-- Each card shows the booked shop, service, status, price, and detail actions. -->
            <div class="customer-stack customer-stack--tight mt-8">
                @forelse ($orders as $order)
                    <article class="customer-card">
                        <div class="customer-card-row customer-card-row--between lg:items-center lg:justify-between">
                            <div>
                                <p class="customer-eyebrow customer-eyebrow--muted">Order #{{ $order->id }}</p>
                                <h2 class="customer-card-title">{{ $order->shop->shop_name }}</h2>
                                <p class="customer-card-copy">{{ $order->shopService->service->name }} · {{ ucfirst(str_replace('_', ' ', $order->service_mode)) }}</p>
                            </div>

                            <div class="customer-inline-meta">
                                <span class="customer-badge customer-badge--status">{{ str_replace('_', ' ', $order->status) }}</span>
                                <span class="customer-price customer-price--small">PHP {{ number_format((float) $order->total_price, 2) }}</span>
                                <a href="{{ route('customer.orders.show', $order) }}" class="customer-button customer-button--dark">View order</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="customer-empty-state">
                        You have not placed any orders yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>