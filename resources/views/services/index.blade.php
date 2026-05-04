<x-app-layout>
    <div class="owner-page">
        <div class="owner-page-container">
        <!-- Services header introduces the pricing workspace for the signed-in owner. -->
        <div class="owner-page-header">
            <div>
                <div class="owner-eyebrow">Management</div>
                <h1 class="owner-page-title">Services & Pricing</h1>
                <p class="owner-page-copy">Manage the fixed service list and pricing for your assigned shop.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="owner-back-link">Back to Dashboard</a>
        </div>

        <x-management-nav />

        @if (session('success'))
            <div class="owner-alert owner-alert--success">
                {{ session('success') }}
            </div>
        @endif

        <div class="owner-panel">
            <div class="owner-subsection">
                <h3 class="owner-subsection-title">Available Services</h3>
                <div class="owner-chip-list">
                    @forelse($services->unique('name') as $service)
                        <span class="owner-chip">{{ $service->name }}</span>
                    @empty
                        <p class="owner-empty-copy">No services available.</p>
                    @endforelse
                </div>
            </div>

            @php($shopServiceCreateErrors = $errors->getBag('shopServiceCreate'))
            <!-- This form assigns one of the fixed service options to a specific shop and sets the customer-facing price. -->
            <div class="owner-subsection">
                <h2 class="owner-section-title mb-4">Assign Service To Shop</h2>
                <form method="POST" action="{{ route('shop-services.store') }}" class="owner-form-grid">
                    @csrf
                    <div>
                        <x-input-label for="shop_id" value="Shop" />
                        <select id="shop_id" name="shop_id" class="owner-form-control" required>
                            <option value="">Select a shop</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" @selected(old('shop_id') == $shop->id)>{{ $shop->shop_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$shopServiceCreateErrors->get('shop_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="service_id" value="Service" />
                        <select id="service_id" name="service_id" class="owner-form-control" required>
                            <option value="">Select a service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$shopServiceCreateErrors->get('service_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="price" value="Price" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 w-full" :value="old('price')" required />
                        <x-input-error :messages="$shopServiceCreateErrors->get('price')" class="mt-2" />
                    </div>
                    <div class="md:col-span-3">
                        <x-primary-button :disabled="$shops->isEmpty() || $services->isEmpty()">Assign Service</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="owner-stack owner-subsection">
                @forelse($shops as $shop)
                    <div>
                        <div class="owner-panel-header">
                            <h3 class="owner-section-title">{{ $shop->shop_name }}</h3>
                            <span class="owner-page-copy">{{ $shop->shopServices->count() }} assigned</span>
                        </div>
                        <div class="owner-table-shell">
                            <table class="owner-table">
                                <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shop->shopServices as $shopService)
                                        <tr>
                                            <td>{{ $shopService->service->name }}</td>
                                            <td>₱{{ number_format((float) $shopService->price, 2) }}</td>
                                            <td>
                                                <form method="POST" action="{{ route('shop-services.destroy', $shopService) }}" data-confirm-submit="Remove this service from the shop?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="owner-danger-button">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="owner-table-empty">No services assigned yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="owner-empty-copy">Create a shop first before assigning services.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>