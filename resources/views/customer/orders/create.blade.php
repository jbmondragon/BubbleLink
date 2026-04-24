<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600">Place Order</p>
                <h1 class="text-3xl font-semibold text-slate-900">{{ $shop->shop_name }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $shop->organization->name }} · {{ $shop->address }}</p>
            </div>
            <a href="{{ route('customer.shops.show', $shop) }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:border-slate-400 hover:text-slate-900">
                Back to shop details
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <section class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200" x-data="{
                selectedPrice: '{{ $services->first()?->price ?? '0.00' }}',
                serviceMode: '{{ old('service_mode', 'both') }}',
                needsPickup() {
                    return ['pickup_only', 'both'].includes(this.serviceMode);
                },
                needsDelivery() {
                    return ['delivery_only', 'both'].includes(this.serviceMode);
                }
            }">
                <form method="POST" action="{{ route('customer.orders.store', $shop) }}" class="space-y-6">
                    @csrf

                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                        Orders are submitted under <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span> using <span class="font-semibold text-slate-900">{{ auth()->user()->email }}</span>.
                    </div>

                    <div>
                        <label for="shop_service_id" class="block text-sm font-medium text-slate-700">Service</label>
                        <select id="shop_service_id" name="shop_service_id" class="mt-2 w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" x-on:change="selectedPrice = $event.target.selectedOptions[0].dataset.price" required>
                            @foreach ($services as $shopService)
                                <option value="{{ $shopService->id }}" data-price="{{ number_format((float) $shopService->price, 2, '.', '') }}" @selected(old('shop_service_id') == $shopService->id)>
                                    {{ $shopService->service->name }} · PHP {{ number_format((float) $shopService->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('shop_service_id', 'customerOrderCreate')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            The shop team will weigh your laundry when they receive it and confirm the final recorded weight for this order.
                        </div>

                        <div>
                            <label for="service_mode" class="block text-sm font-medium text-slate-700">Service mode</label>
                            <select id="service_mode" name="service_mode" x-model="serviceMode" class="mt-2 w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                <option value="pickup_only" @selected(old('service_mode') === 'pickup_only')>Pickup only</option>
                                <option value="delivery_only" @selected(old('service_mode') === 'delivery_only')>Delivery only</option>
                                <option value="both" @selected(old('service_mode', 'both') === 'both')>Pickup and delivery</option>
                                <option value="walk_in" @selected(old('service_mode') === 'walk_in')>Walk-in drop-off and pick-up</option>
                            </select>
                            @error('service_mode', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="serviceMode === 'walk_in'" x-cloak class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        Walk-in means you will bring your laundry to the shop and return to pick it up, so no transport address or transport schedule is needed.
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div x-show="needsPickup()" x-cloak>
                            <label for="pickup_address" class="block text-sm font-medium text-slate-700">Pickup address</label>
                            <input id="pickup_address" name="pickup_address" type="text" value="{{ old('pickup_address') }}" x-bind:disabled="! needsPickup()" class="mt-2 w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('pickup_address', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="needsDelivery()" x-cloak>
                            <label for="delivery_address" class="block text-sm font-medium text-slate-700">Delivery address</label>
                            <input id="delivery_address" name="delivery_address" type="text" value="{{ old('delivery_address') }}" x-bind:disabled="! needsDelivery()" class="mt-2 w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('delivery_address', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div x-show="needsPickup()" x-cloak>
                            <label for="pickup_datetime" class="block text-sm font-medium text-slate-700">Preferred pickup</label>
                            <input id="pickup_datetime" name="pickup_datetime" type="datetime-local" value="{{ old('pickup_datetime') }}" x-bind:disabled="! needsPickup()" class="mt-2 w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('pickup_datetime', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="needsDelivery()" x-cloak>
                            <label for="delivery_datetime" class="block text-sm font-medium text-slate-700">Preferred delivery</label>
                            <input id="delivery_datetime" name="delivery_datetime" type="datetime-local" value="{{ old('delivery_datetime') }}" x-bind:disabled="! needsDelivery()" class="mt-2 w-full rounded-2xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @error('delivery_datetime', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 rounded-3xl bg-emerald-50 px-5 py-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-700">Estimated price</p>
                            <p class="mt-1 text-2xl font-semibold text-emerald-950">PHP <span x-text="selectedPrice"></span></p>
                            <p class="mt-1 text-xs text-emerald-800/80">Final weight is confirmed by shop staff after drop-off or pickup.</p>
                        </div>

                        <button type="submit" class="inline-flex items-center rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-500">
                            Submit order
                        </button>
                    </div>
                </form>
            </section>

            <aside class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Booking summary</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">What happens next</h2>
                    <ol class="mt-4 space-y-3 text-sm text-slate-600">
                        <li>1. The order starts in pending status.</li>
                        <li>2. Staff review your request and update the workflow.</li>
                        <li>3. You can monitor progress from the My Orders page.</li>
                    </ol>
                </section>

                <section class="rounded-3xl bg-slate-900 p-8 text-white shadow-lg">
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Selected branch</p>
                    <h2 class="mt-3 text-2xl font-semibold">{{ $shop->shop_name }}</h2>
                    <p class="mt-3 text-sm text-slate-200">{{ $shop->address }}</p>
                    <p class="mt-2 text-sm text-slate-200">{{ $shop->contact_number ?: 'Contact number not listed' }}</p>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>