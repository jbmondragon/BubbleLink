@if ($search !== '')
    <div class="border-b border-neutral-200 px-6 py-3 text-sm text-neutral-600">
        Filtered by "{{ $search }}"
    </div>
@endif

@if ($shopCards->isEmpty())
    <div class="customer-empty-state">
        No shops matched your search.
    </div>
@else
    <div class="overflow-x-auto">
        <table class="customer-table">
            <thead>
                <tr>
                    <th>Shop</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Popular Services</th>
                    <th>Starting Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($shopCards as $shopCard)
                    @php($shop = $shopCard['shop'])
                    <tr>
                        <td>
                            <p class="customer-eyebrow customer-eyebrow--orange">Laundry shop</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900">{{ $shop->shop_name }}</h2>
                            <p class="mt-3 max-w-sm text-sm text-neutral-700">{{ $shop->description ?: 'Trusted laundry shop with pickup, drop-off, and delivery options.' }}</p>
                            <p class="customer-badge customer-badge--orange mt-3">{{ $shopCard['serviceCount'] }} services</p>
                        </td>
                        <td class="text-sm text-neutral-700">{{ $shop->address }}</td>
                        <td class="text-sm text-neutral-700">{{ $shop->contact_number ?: 'Contact details available on request' }}</td>
                        <td>
                            <div class="flex max-w-xs flex-wrap gap-2">
                                @forelse ($shopCard['featuredServices'] as $shopService)
                                    <span class="customer-badge customer-badge--blue">{{ $shopService->service->name }}</span>
                                @empty
                                    <span class="text-xs text-slate-500">No services listed yet</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-sm font-semibold text-slate-900">PHP {{ number_format((float) $shopCard['startingPrice'], 2) }}</td>
                        <td>
                            <a href="{{ route('customer.shops.show', $shop) }}" class="customer-button customer-button--dark">View details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
