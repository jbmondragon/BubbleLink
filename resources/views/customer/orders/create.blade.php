<x-app-layout>
    <x-slot name="header">
        <!-- Order header anchors the booking flow to the currently selected shop. -->
        <div class="customer-page-header customer-page-header--split">
            <div>
                <p class="customer-eyebrow customer-eyebrow--blue">Place Order</p>
                <h1 class="customer-page-title">{{ $shop->shop_name }}</h1>
                <p class="customer-page-copy">{{ $shop->address }}</p>
            </div>
            <a href="{{ route('customer.shops.show', $shop) }}" class="customer-button customer-button--outline">
                Back to shop details
            </a>
        </div>
    </x-slot>

    <div class="customer-page">
        <div class="customer-page-container max-w-4xl">
            <!-- Alpine keeps service price and pickup/delivery fields in sync with the form selections. -->
            <section class="customer-panel" x-data="customerOrderForm({ selectedPrice: @js(number_format((float) ($services->first()?->price ?? 0), 2, '.', '')), serviceMode: @js(old('service_mode', 'both')) })">
                <form method="POST" action="{{ route('customer.orders.store', $shop) }}" class="space-y-6">
                    @csrf

                    <div class="customer-form-note">
                        Orders are submitted under <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span> using <span class="font-semibold text-slate-900">{{ auth()->user()->email }}</span>.
                    </div>

                    <div>
                        <label for="shop_service_id" class="block text-sm font-medium text-slate-700">Service</label>
                        <select id="shop_service_id" name="shop_service_id" class="customer-form-control" x-on:change="updateSelectedPrice($event)" required>
                            @foreach ($services as $shopService)
                                <option value="{{ $shopService->id }}" data-price="{{ number_format((float) $shopService->price, 2, '.', '') }}" @selected(old('shop_service_id') == $shopService->id)>
                                    {{ $shopService->service->name }} · PHP {{ number_format((float) $shopService->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('shop_service_id', 'customerOrderCreate')
                            <p class="mt-2 text-sm text-neutral-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="customer-grid-two">
                        <div class="customer-form-note customer-form-note--bordered md:col-span-2">
                            The shop team will weigh your laundry when they receive it and confirm the final recorded weight for this order.
                        </div>

                        <div>
                            <label for="service_mode" class="block text-sm font-medium text-slate-700">Service mode</label>
                            <select id="service_mode" name="service_mode" x-model="serviceMode" class="customer-form-control" required>
                                <option value="pickup_only" @selected(old('service_mode') === 'pickup_only')>Pickup only</option>
                                <option value="delivery_only" @selected(old('service_mode') === 'delivery_only')>Delivery only</option>
                                <option value="both" @selected(old('service_mode', 'both') === 'both')>Pickup and delivery</option>
                                <option value="walk_in" @selected(old('service_mode') === 'walk_in')>Walk-in drop-off and pick-up</option>
                            </select>
                            @error('service_mode', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-neutral-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="serviceMode === 'walk_in'" x-cloak class="customer-form-note customer-form-note--success">
                        Walk-in means you will bring your laundry to the shop and return to pick it up, so no transport address or transport schedule is needed.
                    </div>

                    <div class="customer-grid-two">
                        <div x-show="needsPickup()" x-cloak>
                            <label for="pickup_address" class="block text-sm font-medium text-slate-700">Pickup address</label>
                            <input id="pickup_address" name="pickup_address" type="text" value="{{ old('pickup_address') }}" x-bind:disabled="! needsPickup()" class="customer-form-control">
                            @error('pickup_address', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-neutral-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="needsDelivery()" x-cloak>
                            <label for="delivery_address" class="block text-sm font-medium text-slate-700">Delivery address</label>
                            <input id="delivery_address" name="delivery_address" type="text" value="{{ old('delivery_address') }}" x-bind:disabled="! needsDelivery()" class="customer-form-control">
                            @error('delivery_address', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-neutral-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="customer-grid-two">
                        <div x-show="needsPickup()" x-cloak>
                            <label for="pickup_datetime" class="block text-sm font-medium text-slate-700">Preferred pickup</label>
                            <input id="pickup_datetime" name="pickup_datetime" type="datetime-local" value="{{ old('pickup_datetime') }}" x-bind:disabled="! needsPickup()" class="customer-form-control">
                            @error('pickup_datetime', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-neutral-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="needsDelivery()" x-cloak>
                            <label for="delivery_datetime" class="block text-sm font-medium text-slate-700">Preferred delivery</label>
                            <input id="delivery_datetime" name="delivery_datetime" type="datetime-local" value="{{ old('delivery_datetime') }}" x-bind:disabled="! needsDelivery()" class="customer-form-control">
                            @error('delivery_datetime', 'customerOrderCreate')
                                <p class="mt-2 text-sm text-neutral-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="customer-summary-band">
                        <div>
                            <p class="customer-eyebrow text-neutral-700">Estimated price</p>
                            <p class="mt-1 text-2xl font-semibold text-neutral-950">PHP <span x-text="selectedPrice"></span></p>
                            <p class="customer-summary-band-copy">Final weight is confirmed by shop staff after drop-off or pickup.</p>
                        </div>

                        <button type="submit" class="customer-button customer-button--success">
                            Submit order
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>