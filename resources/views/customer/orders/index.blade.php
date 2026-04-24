<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">Customer Orders</p>
                <h1 class="text-3xl font-semibold text-slate-900">My Orders</h1>
                <p class="mt-1 text-sm text-slate-600">Track every order you placed across all shops.</p>
            </div>
            <a href="{{ route('customer.shops.index') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:border-slate-400 hover:text-slate-900">
                Book another service
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-lg">
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Total orders</p>
                    <p class="mt-4 text-4xl font-semibold">{{ $orders->count() }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Pending</p>
                    <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $orders->where('status', 'pending')->count() }}</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Completed</p>
                    <p class="mt-4 text-4xl font-semibold text-slate-900">{{ $orders->where('status', 'completed')->count() }}</p>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                @forelse ($orders as $order)
                    <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Order #{{ $order->id }}</p>
                                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $order->shop->shop_name }}</h2>
                                <p class="mt-1 text-sm text-slate-600">{{ $order->shopService->service->name }} · {{ ucfirst(str_replace('_', ' ', $order->service_mode)) }}</p>
                                @if ($order->shop_rating)
                                    <p class="mt-2 text-sm text-amber-600">Rated {{ $order->shop_rating }}/5</p>
                                @elseif ($order->status === 'completed')
                                    <p class="mt-2 text-sm text-slate-500">Completed order. You can rate this shop from the order details page.</p>
                                @endif
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">{{ str_replace('_', ' ', $order->status) }}</span>
                                <span class="text-sm font-semibold text-slate-900">PHP {{ number_format((float) $order->total_price, 2) }}</span>
                                @if ($order->status === 'completed')
                                    <a href="{{ route('customer.orders.show', $order) }}#shop-rating" class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">
                                        {{ $order->shop_rating ? 'Edit rating' : 'Rate shop' }}
                                    </a>
                                @endif
                                <a href="{{ route('customer.orders.show', $order) }}" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">View order</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                        You have not placed any orders yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>