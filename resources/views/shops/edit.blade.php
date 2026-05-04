<x-app-layout>
    <div class="owner-page">
        <div class="owner-page-container owner-page-container--narrow">
        <!-- Shop edit screen updates the fixed branch information used across the workspace. -->
        <div class="owner-page-header">
            <div>
                <div class="owner-eyebrow">Management</div>
                <h1 class="owner-page-title">Edit Shop</h1>
            </div>
        </div>

        <!-- Existing values are prefilled so the owner can adjust the current branch profile safely. -->
        <form method="POST" action="{{ route('shops.update', $shop) }}" class="owner-panel owner-stack--compact">
            @csrf
            @method('PATCH')

            <div>
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

            <div>
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

            <div>
                <x-input-label for="contact_number" value="Contact Number" />
                <x-text-input
                    id="contact_number"
                    name="contact_number"
                    class="w-full mt-1"
                    :value="old('contact_number', $shop->contact_number)"
                />
                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" value="Description" />
                <x-text-input
                    id="description"
                    name="description"
                    class="w-full mt-1"
                    :value="old('description', $shop->description)"
                />
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="owner-form-actions">
                <x-primary-button>Save Changes</x-primary-button>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>