<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="mb-8 flex items-center justify-between gap-4">
            <div>
                <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Management</div>
                <h1 class="text-3xl font-bold">Services & Pricing</h1>
                <p class="mt-2 text-sm text-slate-600">Manage service types and assign pricing across your organization shops.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-600 hover:underline">Back to Dashboard</a>
        </div>

        <x-management-nav :organization="$organization" :current-role="$currentRole" />

        @if ($currentMembership?->shop)
            <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 px-5 py-4 text-sky-950">
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">Assigned Shop</div>
                <div class="mt-2 text-lg font-semibold">{{ $currentMembership->shop->shop_name }}</div>
                <p class="mt-1 text-sm text-sky-900/75">Service and pricing updates from this page apply within your assigned shop scope.</p>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.1fr,1.4fr]">
            <div class="bg-white shadow rounded-lg p-6">
                @php($serviceCreateErrors = $errors->getBag('serviceCreate'))
                <h2 class="text-lg font-semibold mb-4">Create Service Type</h2>
                <form method="POST" action="{{ route('services.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="service_name" value="Service Name" />
                        <x-text-input id="service_name" name="name" class="mt-1 w-full" :value="old('name')" required />
                        <x-input-error :messages="$serviceCreateErrors->get('name')" class="mt-2" />
                    </div>
                    <x-primary-button>Add Service</x-primary-button>
                </form>

                <div class="mt-6 border-t pt-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-3">Available Services</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($services as $service)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700">{{ $service->name }}</span>
                        @empty
                            <p class="text-sm text-gray-400">No services created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                @php($shopServiceCreateErrors = $errors->getBag('shopServiceCreate'))
                <h2 class="text-lg font-semibold mb-4">Assign Service To Shop</h2>
                <form method="POST" action="{{ route('shop-services.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @csrf
                    <div>
                        <x-input-label for="shop_id" value="Shop" />
                        <select id="shop_id" name="shop_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Select a shop</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" @selected(old('shop_id') == $shop->id)>{{ $shop->shop_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$shopServiceCreateErrors->get('shop_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="service_id" value="Service" />
                        <select id="service_id" name="service_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" required>
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

                <div class="mt-6 space-y-4 border-t pt-4">
                    @forelse($shops as $shop)
                        <div>
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-slate-900">{{ $shop->shop_name }}</h3>
                                <span class="text-sm text-slate-500">{{ $shop->shopServices->count() }} assigned</span>
                            </div>
                            <div class="mt-3 overflow-hidden rounded-lg border border-slate-200">
                                <table class="min-w-full divide-y divide-slate-200 text-sm">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left font-medium text-slate-500">Service</th>
                                            <th class="px-4 py-2 text-left font-medium text-slate-500">Price</th>
                                            <th class="px-4 py-2 text-left font-medium text-slate-500">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        @forelse($shop->shopServices as $shopService)
                                            <tr>
                                                <td class="px-4 py-3">{{ $shopService->service->name }}</td>
                                                <td class="px-4 py-3">₱{{ number_format((float) $shopService->price, 2) }}</td>
                                                <td class="px-4 py-3">
                                                    <form method="POST" action="{{ route('shop-services.destroy', $shopService) }}" onsubmit="return confirm('Remove this service from the shop?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-gray-400">No services assigned yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Create a shop first before assigning services.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>