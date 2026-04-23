<x-app-layout>
    <div class="p-6 max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Create Organization</h1>

        <x-management-nav :organization="null" />

        @if (session('warning'))
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                {{ session('warning') }}
            </div>
        @endif

        @if ($guided ?? false)
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
                Guided setup is active. After creating your organization, you will continue to shop, service, and team setup.
            </div>
        @endif

        <form method="POST" action="/organizations">
            @csrf
            <input type="hidden" name="guided" value="{{ ($guided ?? false) ? 1 : 0 }}">

            <div>
                <x-input-label value="Organization Name" />
                <x-text-input name="name" class="w-full mt-1" required />
            </div>

            <div class="mt-4">
                <x-primary-button>Create</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
