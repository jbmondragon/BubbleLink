<x-app-layout>
    @php
        $hasPickupLeg = in_array($order->service_mode, ['pickup_only', 'both'], true);
        $hasDeliveryLeg = in_array($order->service_mode, ['delivery_only', 'both'], true);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">Order Details</p>
                <h1 class="text-3xl font-semibold text-slate-900">Order #{{ $order->id }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $order->shop->shop_name }} · {{ $order->shop->organization->name }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                @if ($order->status === 'completed')
                    <a href="#shop-rating" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                        {{ $order->shop_rating ? 'Edit rating' : 'Rate shop' }}
                    </a>
                @endif

                <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:border-slate-400 hover:text-slate-900">
                    Back to my orders
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <section class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Service</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->shopService->service->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Status</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Mode</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $order->service_mode)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Price</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">PHP {{ number_format((float) $order->total_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Recorded Weight</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ $order->weight !== null ? number_format((float) $order->weight, 2).' kg' : 'Pending shop measurement' }}</p>
                    </div>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    @if (! $hasPickupLeg && ! $hasDeliveryLeg)
                        <div class="rounded-3xl bg-slate-50 p-6 md:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Walk-in</p>
                            <p class="mt-3 text-sm text-slate-700">You will drop off and pick up your laundry directly at {{ $order->shop->shop_name }}.</p>
                            <p class="mt-2 text-sm text-slate-500">No pickup or delivery transport details are required for this order.</p>
                        </div>
                    @endif

                    @if ($hasPickupLeg)
                        <div class="rounded-3xl bg-slate-50 p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Pickup</p>
                            <p class="mt-3 text-sm text-slate-700">{{ $order->pickup_address ?: 'No pickup address provided' }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ $order->pickup_datetime?->format('M d, Y h:i A') ?: 'No pickup schedule selected' }}</p>
                        </div>
                    @endif

                    @if ($hasDeliveryLeg)
                        <div class="rounded-3xl bg-slate-50 p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Delivery</p>
                            <p class="mt-3 text-sm text-slate-700">{{ $order->delivery_address ?: 'No delivery address provided' }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ $order->delivery_datetime?->format('M d, Y h:i A') ?: 'No delivery schedule selected' }}</p>
                        </div>
                    @endif
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-600 to-cyan-600 p-8 text-white shadow-lg">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/80">Tracking</p>
                    <div class="mt-4 space-y-3 text-sm text-white/90">
                        <p>Current status: <span class="font-semibold text-white">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></p>
                        <p>Payment status: <span class="font-semibold text-white">{{ ucfirst($order->payment_status) }}</span></p>
                        <p>Laundry weight: <span class="font-semibold text-white">{{ $order->weight !== null ? number_format((float) $order->weight, 2).' kg' : 'Pending shop measurement' }}</span></p>
                    </div>
                </section>

                @if ($order->status === 'completed')
                    <section id="shop-rating" class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Shop Rating</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Rate {{ $order->shop->shop_name }}</h2>
                        <p class="mt-3 text-sm text-slate-600">Share your experience with this branch. You can update your rating later if needed.</p>

                        <form method="POST" action="{{ route('customer.orders.rate', $order) }}" class="mt-5 space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="shop_rating" class="block text-sm font-medium text-slate-700">Rating</label>
                                <select id="shop_rating" name="shop_rating" class="mt-2 w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach ([1, 2, 3, 4, 5] as $rating)
                                        <option value="{{ $rating }}" @selected((int) old('shop_rating', $order->shop_rating ?? 5) === $rating)>{{ $rating }} star{{ $rating > 1 ? 's' : '' }}</option>
                                    @endforeach
                                </select>
                                @error('shop_rating', 'customerOrderRating')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($order->shop_rating)
                                <p class="text-sm text-slate-500">Current rating: <span class="font-semibold text-slate-900">{{ $order->shop_rating }}/5</span>{{ $order->rated_at ? ' · updated '.$order->rated_at->format('M d, Y h:i A') : '' }}</p>
                            @endif

                            <button type="submit" class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                {{ $order->shop_rating ? 'Update rating' : 'Submit rating' }}
                            </button>
                        </form>
                    </section>
                @endif

                <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Need another booking?</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">Return to the catalog</h2>
                    <p class="mt-3 text-sm text-slate-600">Browse more branches and place your next order when you are ready.</p>
                    <a href="{{ route('customer.shops.index') }}" class="mt-5 inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:border-slate-400 hover:text-slate-900">Browse shops</a>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>