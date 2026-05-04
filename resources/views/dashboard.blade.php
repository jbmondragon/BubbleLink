
<x-app-layout>
    <div class="owner-page">
        <div class="owner-page-container">

            <!-- Owner dashboard header for the simplified business workspace. -->
            <div class="owner-page-header">
                <div>
                    <p class="owner-eyebrow">Management</p>
                    <h1 class="owner-page-title">My Dashboard</h1>
                </div>
            </div>

            @if (session('success'))
                <div class="owner-alert owner-alert--success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="owner-alert owner-alert--warning">
                    {{ session('warning') }}
                </div>
            @endif

            <x-management-nav />

            <!-- High-level business summary cards pulled from the dashboard controller. -->
            <div class="owner-grid-dashboard">
                <div class="owner-panel text-center">
                    <div class="text-lg font-semibold">Total Orders</div>
                    <div class="mt-1 text-2xl">{{ $totalOrders ?? 0 }}</div>
                </div>
                <div class="owner-panel text-center">
                    <div class="text-lg font-semibold">Total Revenue</div>
                    <div class="mt-1 text-2xl">₱{{ number_format($totalRevenue ?? 0, 2) }}</div>
                </div>
                <div class="owner-panel text-center">
                    <div class="text-lg font-semibold">Number of Shops</div>
                    <div class="mt-1 text-2xl">{{ $shopCount ?? 0 }}</div>
                </div>
            </div>

            <!-- Shop workspace reminds owners that shops are the fixed base for daily operations. -->
            <div class="owner-panel">
                <div class="owner-panel-header">
                    <div>
                        <h2 class="owner-section-title">Shop Workspace</h2>
                        <p class="owner-section-copy">Your shop profile is fixed for day-to-day operations. Use this workspace to add services and manage incoming orders.</p>
                    </div>

                    @if (($shopCount ?? 0) === 0)
                        <a href="{{ route('shops.create') }}" class="inline-flex items-center rounded-md bg-neutral-950 px-4 py-2 text-sm font-medium text-white transition hover:bg-neutral-800">Create First Shop</a>
                    @endif
                </div>

                @if (($shops ?? collect())->isNotEmpty())
                    <div class="owner-chip-list">
                        @foreach($shops as $shop)
                            <span class="owner-chip">{{ $shop->shop_name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-sm text-neutral-600">Create your first shop once, then continue everything else from Services and Orders.</p>
                @endif
            </div>

            <!-- Quick links route owners into the two main operational surfaces: services and orders. -->
            <div class="owner-grid-two">
                <a href="{{ route('services.index') }}" class="owner-summary-link">
                    <div class="owner-eyebrow">Services</div>
                    <div class="owner-summary-value">{{ $assignedServiceCount }}</div>
                    <p class="owner-section-copy">Add services and assign pricing for the shops already linked to your account.</p>
                </a>
                <a href="{{ route('orders.index') }}" class="owner-summary-link">
                    <div class="owner-eyebrow">Orders</div>
                    <div class="owner-summary-value">{{ $totalOrders }}</div>
                    <p class="owner-section-copy">Accept orders, create internal orders, and update order and payment progress.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
