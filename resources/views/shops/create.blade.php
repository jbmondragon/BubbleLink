<x-app-layout>
    <div class="owner-page">
        <div class="owner-page-container owner-page-container--narrow">
        <!-- Simple shop creation screen used when the owner still has no shop record. -->
        <div class="owner-page-header">
            <div>
                <div class="owner-eyebrow">Management</div>
                <h1 class="owner-page-title">Create Shop</h1>
            </div>
        </div>
        <!-- This form captures the branch profile that anchors later service and order management. -->
        <form method="POST" action="{{ route('shops.store') }}" class="owner-panel owner-stack--compact">
            @csrf
            <div>
                <x-input-label value="Shop Name" />
                <x-text-input name="shop_name" class="w-full mt-1" required />
            </div>
            <div>
                <x-input-label value="Address" />
                <x-text-input name="address" class="w-full mt-1" required />
            </div>
            <div>
                <x-input-label value="Contact Number" />
                <x-text-input name="contact_number" class="w-full mt-1" />
            </div>
            <div>
                <x-input-label value="Description" />
                <x-text-input name="description" class="w-full mt-1" />
            </div>
            <div>
                <x-primary-button>Create</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
