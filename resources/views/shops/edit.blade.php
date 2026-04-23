<x-app-layout>
    <div class="p-6 max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Edit Shop</h1>

        <form method="POST" action="{{ route('shops.update', $shop) }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <x-input-label for="shop_name" value="Shop Name" />
                <x-text-input
                    id="shop_name"
                    name="shop_name"
                    class="w-full mt-1"
                    :value="old('shop_name', $shop->shop_name)"
                    required
                />
                <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="address" value="Address" />
                <x-text-input
                    id="address"
                    name="address"
                    class="w-full mt-1"
                    :value="old('address', $shop->address)"
                    required
                />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="contact_number" value="Contact Number" />
                <x-text-input
                    id="contact_number"
                    name="contact_number"
                    class="w-full mt-1"
                    :value="old('contact_number', $shop->contact_number)"
                />
                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="description" value="Description" />
                <x-text-input
                    id="description"
                    name="description"
                    class="w-full mt-1"
                    :value="old('description', $shop->description)"
                />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4 flex items-center gap-3">
                <x-primary-button>Save Changes</x-primary-button>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>