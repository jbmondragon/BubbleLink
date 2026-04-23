<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">Order Details</p>
                <h1 class="text-3xl font-semibold text-slate-900">Order #{{ $order->id }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $order->shop->shop_name }} · {{ $order->shop->organization->name }}</p>
            </div>
            <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:border-slate-400 hover:text-slate-900">
                Back to my orders
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <section class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
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
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    <div class="rounded-3xl bg-slate-50 p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Pickup</p>
                        <p class="mt-3 text-sm text-slate-700">{{ $order->pickup_address ?: 'No pickup address provided' }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $order->pickup_datetime?->format('M d, Y h:i A') ?: 'No pickup schedule selected' }}</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Delivery</p>
                        <p class="mt-3 text-sm text-slate-700">{{ $order->delivery_address ?: 'No delivery address provided' }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $order->delivery_datetime?->format('M d, Y h:i A') ?: 'No delivery schedule selected' }}</p>
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-600 to-cyan-600 p-8 text-white shadow-lg">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/80">Tracking</p>
                    <div class="mt-4 space-y-3 text-sm text-white/90">
                        <p>Current status: <span class="font-semibold text-white">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></p>
                        <p>Payment status: <span class="font-semibold text-white">{{ ucfirst($order->payment_status) }}</span></p>
                        <p>Laundry weight: <span class="font-semibold text-white">{{ number_format((float) $order->weight, 2) }} kg</span></p>
                    </div>
                </section>

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