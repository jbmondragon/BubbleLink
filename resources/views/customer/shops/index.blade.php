<x-app-layout>
    {{--
        SID2 Frontend: Customer shop marketplace.
        This page lets customers browse and search available laundry shops before choosing one.
    --}}

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
                            <div class="customer-hero-actions sm:hidden">
                                <a href="{{ route('unified.login') }}" class="customer-button customer-button--hero-light">
                                    Login
                                </a>
                            </div>
                        @endguest
                    </div>

                    <form method="GET" action="{{ route('customer.shops.index') }}" x-data="ajaxSearch" @submit="submitSearch" class="customer-search-panel">
                        <label for="search" class="customer-search-label">Search shops</label>
                        <div class="customer-search-row">
                            <input id="search" name="search" value="{{ $search }}" placeholder="Search by shop name or service" class="customer-search-input">
                            <button type="submit" x-text="loading ? 'Searching...' : 'Search'" :disabled="loading" class="customer-button customer-button--hero-light customer-button--search-align">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main results table is populated from controller-prepared shop card data. -->
            <div id="shop-table-container" class="customer-table-shell">
                @include('customer.shops._table', ['shopCards' => $shopCards, 'search' => $search])
            </div>
        </div>
    </div>
</x-app-layout>