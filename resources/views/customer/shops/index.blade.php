<x-app-layout>
    <x-slot name="header">
        <!-- Marketplace header introduces the public catalog experience. -->
        <div>
            <div>
                <p class="customer-eyebrow customer-eyebrow--orange">Laundry Marketplace</p>
                <h1 class="customer-page-title">Find a shop near you</h1>
                <p class="customer-page-copy">Browse available shops, compare services, and place your order online.</p>
            </div>
        </div>
    </x-slot>

    <div class="customer-page">
        <div class="customer-page-container">
            <!-- Hero combines marketing copy with the live search form used to filter shops. -->
            <div class="app-hero overflow-hidden rounded-3xl px-6 py-8 text-white sm:px-8">
                <div class="grid gap-5 lg:grid-cols-[1.4fr_0.8fr] lg:items-end lg:gap-6">
                    <div>
                        <!-- <p class="customer-eyebrow text-neutral-200">Fresh pickup and delivery</p> -->
                        <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Fresh pickup and delivery.</h2>
                        <p class="mt-3 max-w-2xl text-sm text-neutral-200">Search the catalog, open a shop page, review pricing, then place your order using your BubbleLink account.</p>

                        @guest
                            <div class="customer-hero-actions sm:hidden min-[480px]:grid-cols-3">
                                <a href="{{ route('customer.login') }}" class="customer-button customer-button--hero-light">
                                    Customer Login
                                </a>
                                <a href="{{ route('admin.login') }}" class="customer-button customer-button--hero-dark">
                                    Shop Owner Login
                                </a>
                                <a href="{{ route('platform-admin.login') }}" class="customer-button customer-button--hero-outline">
                                    Admin Login
                                </a>
                            </div>
                        @endguest
                    </div>

                    <form method="GET" action="{{ route('customer.shops.index') }}" class="customer-search-panel">
                        <label for="search" class="customer-search-label">Search shops</label>
                        <div class="customer-search-row">
                            <input id="search" name="search" value="{{ $search }}" placeholder="Search by shop name or service" class="customer-search-input">
                            <button type="submit" class="customer-button customer-button--hero-light customer-button--search-align">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main results table is populated from controller-prepared shop card data. -->
            <div class="customer-table-shell">
                @if ($search !== '')
                    <div class="border-b border-neutral-200 px-6 py-3 text-sm text-neutral-600">
                        Filtered by "{{ $search }}"
                    </div>
                @endif

                @if ($shopCards->isEmpty())
                    <div class="customer-empty-state">
                        No shops matched your search.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="customer-table">
                            <thead>
                                <tr>
                                    <th>Shop</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Popular Services</th>
                                    <th>Starting Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shopCards as $shopCard)
                                    @php($shop = $shopCard['shop'])
                                    <tr>
                                        <td>
                                            <p class="customer-eyebrow customer-eyebrow--orange">Laundry shop</p>
                                            <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $shop->shop_name }}</h2>
                                            <p class="mt-3 max-w-sm text-sm text-neutral-700">{{ $shop->description ?: 'Trusted laundry shop with pickup, drop-off, and delivery options.' }}</p>
                                            <p class="customer-badge customer-badge--orange mt-3">{{ $shopCard['serviceCount'] }} services</p>
                                        </td>
                                        <td class="text-sm text-neutral-700">{{ $shop->address }}</td>
                                        <td class="text-sm text-neutral-700">{{ $shop->contact_number ?: 'Contact details available on request' }}</td>
                                        <td>
                                            <div class="flex max-w-xs flex-wrap gap-2">
                                                @forelse ($shopCard['featuredServices'] as $shopService)
                                                    <span class="customer-badge customer-badge--blue">{{ $shopService->service->name }}</span>
                                                @empty
                                                    <span class="text-xs text-slate-500">No services listed yet</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="text-sm font-semibold text-slate-900">PHP {{ number_format((float) $shopCard['startingPrice'], 2) }}</td>
                                        <td>
                                            <a href="{{ route('customer.shops.show', $shop) }}" class="customer-button customer-button--dark">View details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>