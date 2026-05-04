<x-app-layout>
    <x-slot name="header">
        <!-- Shop detail header identifies the shop the customer is currently reviewing. -->
        <div class="customer-page-header customer-page-header--split">
            <div>
                <p class="customer-eyebrow customer-eyebrow--blue">Laundry shop</p>
                <h1 class="customer-page-title">{{ $shop->shop_name }}</h1>
                <p class="customer-page-copy">{{ $shop->address }}</p>
            </div>
            <a href="{{ route('customer.shops.index') }}" class="customer-button customer-button--outline">
                Back to shops
            </a>
        </div>
    </x-slot>

    <div class="customer-page">
        <div class="customer-page-container max-w-5xl">
            <section class="customer-panel">
                <div class="customer-grid-two">
                    <div>
                        <p class="customer-eyebrow customer-eyebrow--muted">Address</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $shop->address }}</p>
                    </div>
                    <div>
                        <p class="customer-eyebrow customer-eyebrow--muted">Contact number</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $shop->contact_number ?: 'Not listed' }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="customer-page-header customer-page-header--split">
                        <div>
                            <h2 class="text-2xl font-semibold text-slate-900">Services</h2>
                            <p class="mt-2 text-sm text-slate-600">{{ $serviceCount }} available</p>
                        </div>

                        @auth
                            <a href="{{ route('customer.orders.create', $shop) }}" class="customer-button customer-button--success">
                                Place order
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="customer-button customer-button--dark">
                                Log in to order
                            </a>
                        @endauth
                    </div>

                    <div class="customer-table-shell mt-6">
                        <div class="overflow-x-auto">
                            <table class="customer-table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $serviceCard)
                                        <tr>
                                            <td class="font-semibold text-slate-900">{{ $serviceCard['shopService']->service->name }}</td>
                                            <td class="text-sm font-semibold text-slate-900">PHP {{ number_format((float) $serviceCard['shopService']->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>