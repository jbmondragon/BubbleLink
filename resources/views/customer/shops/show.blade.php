<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">{{ $shop->organization->name }}</p>
                <h1 class="text-3xl font-semibold text-slate-900">{{ $shop->shop_name }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $shop->address }}</p>
            </div>
            <a href="{{ route('customer.shops.index') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:border-slate-400 hover:text-slate-900">
                Back to shops
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <section class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-3xl bg-slate-900 p-6 text-white">
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Branch overview</p>
                        <p class="mt-4 text-3xl font-semibold">Laundry support for pickup, drop-off, and delivery.</p>
                        <p class="mt-4 text-sm text-slate-200">{{ $shop->description ?: 'Book orders online and keep track of the latest status from one place.' }}</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <dl class="space-y-4 text-sm text-slate-600">
                            <div>
                                <dt class="font-semibold text-slate-900">Address</dt>
                                <dd>{{ $shop->address }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-900">Contact number</dt>
                                <dd>{{ $shop->contact_number ?: 'Not listed' }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-900">Services available</dt>
                                <dd>{{ $services->count() }}</dd>
                            </div>
                            <div>
                                <dt class="font-semibold text-slate-900">Customer rating</dt>
                                <dd>{{ $shop->ratings_count > 0 ? number_format((float) $shop->average_rating, 1).' / 5 from '.$shop->ratings_count.' rating'.($shop->ratings_count === 1 ? '' : 's') : 'No ratings yet' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Service menu</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">What you can book here</h2>
                        </div>

                        @auth
                            <a href="{{ route('customer.orders.create', $shop) }}" class="inline-flex items-center rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                Place order
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-700">
                                Log in to order
                            </a>
                        @endauth
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($services as $shopService)
                            <div class="rounded-3xl border border-slate-200 p-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">{{ $shopService->service->name }}</h3>
                                        <p class="mt-1 text-sm text-slate-600">Available for {{ str_replace('_', ' + ', $shopService->orders->isNotEmpty() ? 'pickup_only' : 'pickup_only / delivery_only / both') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Price</p>
                                        <p class="mt-1 text-2xl font-semibold text-slate-900">PHP {{ number_format((float) $shopService->price, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-3xl bg-gradient-to-br from-cyan-600 via-emerald-600 to-emerald-700 p-8 text-white shadow-lg">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/80">Order flow</p>
                    <ol class="mt-4 space-y-4 text-sm text-white/90">
                        <li>1. Pick a service from the list.</li>
                        <li>2. Choose walk-in, pickup, delivery, or both.</li>
                        <li>3. Submit only the transport details needed for your selected option.</li>
                        <li>4. Track progress from the My Orders page.</li>
                    </ol>
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Need to compare first?</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">Browse more branches</h2>
                    <p class="mt-3 text-sm text-slate-600">Open the catalog again to compare available branches and service pricing.</p>
                    <a href="{{ route('customer.shops.index') }}" class="mt-5 inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-400 hover:text-slate-900">Browse shops</a>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>