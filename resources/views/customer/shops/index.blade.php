<x-app-layout>
    <x-slot name="header">
        <div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600">Laundry Marketplace</p>
                <h1 class="text-3xl font-semibold text-slate-900">Find a shop near you</h1>
                <p class="mt-1 text-sm text-teal-900/70">Browse available branches, compare services, and place your order online.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="app-hero overflow-hidden rounded-3xl px-6 py-8 text-white sm:px-8">
                <div class="grid gap-5 lg:grid-cols-[1.4fr_0.8fr] lg:items-end lg:gap-6">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-amber-100">Fresh pickup and delivery</p>
                        <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Book laundry services without calling every branch.</h2>
                        <p class="mt-3 max-w-2xl text-sm text-orange-50/90">Search the catalog, open a shop page, review pricing, then place your order using your BubbleLink account.</p>

                        @guest
                            <div class="mt-5 grid grid-cols-1 gap-3 sm:hidden min-[480px]:grid-cols-3">
                                <a href="{{ route('customer.login') }}" class="inline-flex items-center justify-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-teal-950 transition hover:bg-orange-50">
                                    Customer Login
                                </a>
                                <a href="{{ route('admin.login') }}" class="inline-flex items-center justify-center rounded-full bg-teal-950/70 px-5 py-3 text-sm font-semibold text-orange-50 transition hover:bg-teal-950">
                                    Shop Owner Login
                                </a>
                                <a href="{{ route('platform-admin.login') }}" class="inline-flex items-center justify-center rounded-full border border-white/40 px-5 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10">
                                    Admin Login
                                </a>
                            </div>
                        @endguest
                    </div>

                    <form method="GET" action="{{ route('customer.shops.index') }}" class="rounded-3xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                        <label for="search" class="block text-sm font-medium text-orange-50">Search shops</label>
                        <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                            <input id="search" name="search" value="{{ $search }}" placeholder="Search by branch, address, or description" class="w-full rounded-2xl border border-white/20 bg-white/90 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-500 focus:border-white focus:outline-none focus:ring-2 focus:ring-white/40">
                            <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-teal-950 transition hover:bg-orange-50 sm:self-auto">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-3xl border border-orange-100 bg-white/85 shadow-sm backdrop-blur">
                @if ($shops->isEmpty())
                    <div class="app-panel rounded-3xl border-dashed p-10 text-center text-teal-800/70">
                        No shops matched your search.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-orange-100">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Shop</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Address</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Contact</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Popular Services</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Starting Price</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-orange-100 bg-white/80">
                                @foreach ($shops as $shop)
                                    <tr class="align-top">
                                        <td class="px-6 py-5">
                                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">{{ $shop->organization->name }}</p>
                                            <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $shop->shop_name }}</h2>
                                            <p class="mt-2 text-sm text-amber-600">{{ $shop->ratings_count > 0 ? number_format((float) $shop->average_rating, 1).' / 5 · '.$shop->ratings_count.' rating'.($shop->ratings_count === 1 ? '' : 's') : 'No ratings yet' }}</p>
                                            <p class="mt-3 max-w-sm text-sm text-teal-900/70">{{ $shop->description ?: 'Trusted laundry branch with pickup, drop-off, and delivery options.' }}</p>
                                            <p class="mt-3 inline-flex rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">{{ $shop->shopServices->count() }} services</p>
                                        </td>
                                        <td class="px-6 py-5 text-sm text-teal-900/80">{{ $shop->address }}</td>
                                        <td class="px-6 py-5 text-sm text-teal-900/80">{{ $shop->contact_number ?: 'Contact details available on request' }}</td>
                                        <td class="px-6 py-5">
                                            <div class="flex max-w-xs flex-wrap gap-2">
                                                @forelse ($shop->shopServices->take(3) as $shopService)
                                                    <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800">{{ $shopService->service->name }}</span>
                                                @empty
                                                    <span class="text-xs text-slate-500">No services listed yet</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-sm font-semibold text-slate-900">PHP {{ number_format((float) $shop->shopServices->min('price'), 2) }}</td>
                                        <td class="px-6 py-5">
                                            <a href="{{ route('customer.shops.show', $shop) }}" class="inline-flex items-center rounded-full bg-teal-900 px-4 py-2 text-sm font-semibold text-orange-50 transition hover:bg-teal-800">View details</a>
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