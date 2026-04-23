<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="mb-8 flex items-center justify-between gap-4">
            <div>
                <div class="text-sm font-semibold uppercase tracking-wide text-slate-500">Management</div>
                <h1 class="text-3xl font-bold">Memberships & Staff</h1>
                <p class="mt-2 text-sm text-slate-600">Invite staff, update organization roles, and filter your member directory.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-600 hover:underline">Back to Dashboard</a>
        </div>

        <x-management-nav :organization="$organization" :current-role="$currentRole" />

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('memberInvite'))
            <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-amber-900">
                <div class="text-sm font-semibold uppercase tracking-wide text-amber-700">New Member Invite</div>
                <div class="mt-2 text-sm">Share this password setup link with the new member so they can create their own password.</div>
                <div class="mt-3 grid gap-2 text-sm md:grid-cols-3">
                    <div>
                        <div class="font-medium text-amber-700">Name</div>
                        <div>{{ session('memberInvite.name') }}</div>
                    </div>
                    <div>
                        <div class="font-medium text-amber-700">Email</div>
                        <div>{{ session('memberInvite.email') }}</div>
                    </div>
                    <div>
                        <div class="font-medium text-amber-700">Password Setup Link</div>
                        <a href="{{ session('memberInvite.reset_url') }}" class="break-all font-mono text-amber-900 underline">{{ session('memberInvite.reset_url') }}</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.1fr,1.4fr]">
            <div class="bg-white shadow rounded-lg p-6">
                @php($membershipCreateErrors = $errors->getBag('membershipCreate'))
                <h2 class="text-lg font-semibold mb-4">Add Member</h2>
                <form method="POST" action="{{ route('memberships.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="member_name" value="Full Name" />
                        <x-text-input id="member_name" name="name" class="mt-1 w-full" :value="old('name')" required />
                        <x-input-error :messages="$membershipCreateErrors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="member_email" value="Email" />
                        <x-text-input id="member_email" name="email" type="email" class="mt-1 w-full" :value="old('email')" required />
                        <x-input-error :messages="$membershipCreateErrors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="member_contact_number" value="Contact Number" />
                        <x-text-input id="member_contact_number" name="contact_number" class="mt-1 w-full" :value="old('contact_number')" />
                        <x-input-error :messages="$membershipCreateErrors->get('contact_number')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="member_role" value="Role" />
                        <select id="member_role" name="role" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="manager" @selected(old('role') === 'manager')>Manager</option>
                            <option value="staff" @selected(old('role') === 'staff')>Staff</option>
                        </select>
                        <x-input-error :messages="$membershipCreateErrors->get('role')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="member_shop_id" value="Assigned Shop" />
                        <select id="member_shop_id" name="shop_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Select a shop</option>
                            @foreach ($shops as $shop)
                                <option value="{{ $shop->id }}" @selected(old('shop_id') == $shop->id)>{{ $shop->shop_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$membershipCreateErrors->get('shop_id')" class="mt-2" />
                    </div>
                    <x-primary-button :disabled="! $organization">Add Member</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold">Current Members</h2>
                    <span class="text-sm text-slate-500">{{ $memberCount }} total</span>
                </div>

                <form method="GET" action="{{ route('memberships.index') }}" class="mb-4 grid grid-cols-1 gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 md:grid-cols-[minmax(0,1.6fr),minmax(0,1fr),auto] md:items-end">
                    <div>
                        <x-input-label for="member_search" value="Search Members" />
                        <x-text-input id="member_search" name="member_search" class="mt-1 w-full" :value="$memberSearch" placeholder="Name, email, contact, or role" />
                    </div>
                    <div>
                        <x-input-label for="member_role_filter" value="Role" />
                        <select id="member_role_filter" name="member_role" class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                            <option value="" @selected($memberRoleFilter === '')>All Roles</option>
                            <option value="owner" @selected($memberRoleFilter === 'owner')>Owner</option>
                            <option value="manager" @selected($memberRoleFilter === 'manager')>Manager</option>
                            <option value="staff" @selected($memberRoleFilter === 'staff')>Staff</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-primary-button>Filter</x-primary-button>
                        @if ($memberSearch !== '' || $memberRoleFilter !== '')
                            <a href="{{ route('memberships.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Clear</a>
                        @endif
                    </div>
                </form>

                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Member</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Contact</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Role</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Assigned Shop</th>
                                <th class="px-4 py-3 text-left font-medium text-slate-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($memberships as $membership)
                                @php($membershipUpdateErrors = $errors->getBag('membershipUpdate-'.$membership->id))
                                @php($failedMembershipId = old('membership_id'))
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900">{{ $membership->user?->name ?? 'Unknown user' }}</div>
                                        <div class="text-xs text-slate-500">{{ $membership->user?->email }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $membership->user?->contact_number ?? 'No contact number' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($membership->role !== 'owner')
                                            <select name="role" form="membership-update-{{ $membership->id }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                                                @foreach(['manager', 'staff'] as $roleOption)
                                                    <option value="{{ $roleOption }}" @selected(($failedMembershipId == $membership->id ? old('role', $membership->role) : $membership->role) === $roleOption)>
                                                        {{ ucfirst($roleOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$membershipUpdateErrors->get('role')" class="mt-2" />
                                        @else
                                            {{ ucfirst($membership->role) }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($membership->role !== 'owner')
                                            <select name="shop_id" form="membership-update-{{ $membership->id }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                                                @foreach ($shops as $shop)
                                                    <option value="{{ $shop->id }}" @selected(($failedMembershipId == $membership->id ? old('shop_id', $membership->shop_id) : $membership->shop_id) == $shop->id)>
                                                        {{ $shop->shop_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$membershipUpdateErrors->get('shop_id')" class="mt-2" />
                                        @else
                                            <span class="text-slate-400">All shops</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($membership->role !== 'owner')
                                            <div class="flex items-center gap-3">
                                                <form id="membership-update-{{ $membership->id }}" method="POST" action="{{ route('memberships.update', $membership) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                                                    <x-primary-button>Save</x-primary-button>
                                                </form>
                                                <form method="POST" action="{{ route('memberships.destroy', $membership) }}" onsubmit="return confirm('Remove this member from the organization?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-slate-400">Owner</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-gray-400">
                                        {{ $memberSearch !== '' || $memberRoleFilter !== '' ? 'No members match the current filter.' : 'No members found for this organization.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>