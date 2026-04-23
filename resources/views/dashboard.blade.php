
<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4">

        <h1 class="text-3xl font-bold mb-8">Organization Dashboard</h1>

        @if (empty($organization))
            <div class="mb-10 rounded-lg border border-amber-200 bg-amber-50 px-6 py-5 text-amber-900 flex items-center justify-between">
                <div>
                    <div class="text-lg font-semibold">You have not created an organization yet.</div>
                    <div class="mt-1 text-sm">To access management features, create your organization first.</div>
                </div>
                <a href="{{ route('organizations.create') }}" class="ml-6 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Organization</a>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900">
                {{ session('warning') }}
            </div>
        @endif

        <x-management-nav :organization="$organization" :current-role="$currentRole" />

        @if ($currentMembership?->shop)
            <div class="mb-8 rounded-xl border border-sky-200 bg-sky-50 px-5 py-4 text-sky-950">
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">Assigned Shop</div>
                <div class="mt-2 flex items-center justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold">{{ $currentMembership->shop->shop_name }}</div>
                        <div class="mt-1 text-sm text-sky-900/75">Your {{ $currentRole }} access is currently scoped to this shop.</div>
                    </div>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700 shadow-sm">{{ ucfirst($currentRole) }}</span>
                </div>
            </div>
        @endif

        <!-- Dashboard Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center">
                <div class="text-4xl">📦</div>
                <div class="text-lg font-semibold mt-2">Total Orders</div>
                <div class="text-2xl mt-1">{{ $totalOrders ?? 0 }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center">
                <div class="text-4xl">💰</div>
                <div class="text-lg font-semibold mt-2">Total Revenue</div>
                <div class="text-2xl mt-1">₱{{ number_format($totalRevenue ?? 0, 2) }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center">
                <div class="text-4xl">🏪</div>
                <div class="text-lg font-semibold mt-2">Number of Shops</div>
                <div class="text-2xl mt-1">{{ $shopCount ?? 0 }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center">
                <div class="text-4xl">👥</div>
                <div class="text-lg font-semibold mt-2">Members</div>
                <div class="text-2xl mt-1">{{ $memberCount ?? 0 }}</div>
            </div>
        </div>

        <!-- Shop Management -->
        <div class="mb-10">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Shops</h2>
                @if (! $organization)
                    <a href="{{ route('organizations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Organization</a>
                @elseif ($canManageShops)
                    <a href="{{ route('shops.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Create Shop</a>
                @endif
            </div>
            <div class="bg-white shadow rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shops ?? [] as $shop)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if (in_array($currentRole, ['owner', 'manager', 'staff'], true))
                                    <a href="{{ route('shops.show', $shop) }}" class="font-medium text-slate-900 hover:text-blue-600 hover:underline">{{ $shop->shop_name }}</a>
                                @else
                                    <span class="font-medium text-slate-900">{{ $shop->shop_name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if (in_array($currentRole, ['owner', 'manager', 'staff'], true))
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('shops.show', $shop) }}" class="text-slate-700 hover:underline">View</a>
                                        @if ($canManageShops)
                                            <a href="{{ route('shops.edit', $shop) }}" class="text-blue-600 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('shops.destroy', $shop) }}" onsubmit="return confirm('Delete this shop?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-slate-500">Dashboard only</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-400">No shops found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-3">
            @if ($canManageServices)
                <a href="{{ route('services.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Services</div>
                    <div class="mt-3 text-2xl font-bold text-slate-900">{{ $serviceTypeCount }}</div>
                    <p class="mt-2 text-sm text-slate-600">Manage service types and shop pricing. {{ $assignedServiceCount }} shop service assignments currently active.</p>
                </a>
            @else
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Services</div>
                    <div class="mt-3 text-2xl font-bold text-slate-900">{{ $serviceTypeCount }}</div>
                    <p class="mt-2 text-sm text-slate-600">{{ $organization ? 'Owners can review service totals here, while managers handle service setup and pricing.' : 'Create your organization first to unlock service and pricing management.' }}</p>
                </div>
            @endif
            @if ($canManageOrders)
                <a href="{{ route('orders.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Orders</div>
                    <div class="mt-3 text-2xl font-bold text-slate-900">{{ $totalOrders }}</div>
                    <p class="mt-2 text-sm text-slate-600">Review customer orders, update statuses, and track payment progress from a dedicated page.</p>
                </a>
            @else
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Orders</div>
                    <div class="mt-3 text-2xl font-bold text-slate-900">{{ $totalOrders }}</div>
                    <p class="mt-2 text-sm text-slate-600">{{ $organization ? 'Owners can monitor order totals here, while managers and staff handle order updates.' : 'Create your organization first to unlock order management.' }}</p>
                </div>
            @endif
            @if ($canManageMemberships)
                <a href="{{ route('memberships.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Members</div>
                    <div class="mt-3 text-2xl font-bold text-slate-900">{{ $memberCount }}</div>
                    <p class="mt-2 text-sm text-slate-600">Invite staff, update roles, and filter the organization directory on its own page.</p>
                </a>
            @else
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                    <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Members</div>
                    <div class="mt-3 text-2xl font-bold text-slate-900">{{ $memberCount }}</div>
                    <p class="mt-2 text-sm text-slate-600">{{ $organization ? 'Only owners can manage organization members and role assignments.' : 'Create your organization first to unlock member management.' }}</p>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
