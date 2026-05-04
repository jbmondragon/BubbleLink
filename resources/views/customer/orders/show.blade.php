<x-app-layout>
    <x-slot name="header">
        <!-- Order detail header keeps primary actions close to the current order context. -->
        <div class="customer-page-header customer-page-header--split">
            <div>
                <p class="customer-eyebrow customer-eyebrow--blue">Order Details</p>
                <h1 class="customer-page-title">Order #{{ $order->id }}</h1>
                <p class="customer-page-copy">{{ $order->shop->shop_name }}</p>
            </div>
            <div class="customer-split-actions">
                <a href="{{ route('customer.orders.index') }}" class="customer-button customer-button--outline">
                    Back to my orders
                </a>
            </div>
        </div>
    </x-slot>

    <div class="customer-page">
        <div class="customer-page-container max-w-5xl">
            <!-- Main panel shows service, status, price, weight, and the active pickup or delivery legs. -->
            <section class="customer-panel">
                @if (session('success'))
                    <div class="customer-notice">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="customer-grid-two">
                    <div>
                        <p class="customer-eyebrow customer-eyebrow--muted">Service</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->shopService->service->name }}</p>
                    </div>
                    <div>
                        <p class="customer-eyebrow customer-eyebrow--muted">Status</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
                    </div>
                    <div>
                        <p class="customer-eyebrow customer-eyebrow--muted">Price</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">PHP {{ number_format((float) $order->total_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="customer-eyebrow customer-eyebrow--muted">Recorded Weight</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ $order->weight !== null ? number_format((float) $order->weight, 2).' kg' : 'Pending shop measurement' }}</p>
                    </div>
                </div>

                <div class="mt-8 border-t border-neutral-200 pt-6">
                    <p class="customer-eyebrow customer-eyebrow--muted">Need another booking?</p>
                    <p class="mt-3 text-sm text-slate-700">Browse more shops and place your next order when you are ready.</p>
                    <a href="{{ route('customer.shops.index') }}" class="customer-button customer-button--outline mt-5">Browse shops</a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>