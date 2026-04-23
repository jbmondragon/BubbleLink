<x-app-layout>
    <div class="p-6 max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Create Shop</h1>
        <form method="POST" action="{{ route('shops.store') }}">
            @csrf
            <input type="hidden" name="organization_id" value="{{ $organization?->id }}">
            <div class="mb-4">
                <x-input-label value="Shop Name" />
                <x-text-input name="shop_name" class="w-full mt-1" required />
            </div>
            <div class="mb-4">
                <x-input-label value="Address" />
                <x-text-input name="address" class="w-full mt-1" required />
            </div>
            <div class="mb-4">
                <x-input-label value="Contact Number" />
                <x-text-input name="contact_number" class="w-full mt-1" />
            </div>
            <div class="mb-4">
                <x-input-label value="Description" />
                <x-text-input name="description" class="w-full mt-1" />
            </div>
            <div class="mt-4">
                <x-primary-button>Create</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
