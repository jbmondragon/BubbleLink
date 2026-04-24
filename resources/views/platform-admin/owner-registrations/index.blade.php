<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-slate-900 px-8 py-10 text-white shadow-lg">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-200">Platform Admin</p>
                <h1 class="mt-3 text-4xl font-semibold">Shop owner approval queue</h1>
                <p class="mt-4 max-w-2xl text-sm text-slate-200">Review new shop owner registrations and approve or reject them before they can access organization setup.</p>
            </div>

            @if (session('success'))
                <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <section class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Pending approvals</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Review incoming shop owner requests</h2>
                    </div>
                    <span class="rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700">{{ $pendingOwnerRegistrations->count() }} pending</span>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($pendingOwnerRegistrations as $ownerRegistration)
                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $ownerRegistration->name }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $ownerRegistration->email }}</p>
                                    <p class="mt-2 text-sm text-slate-500">Registered {{ $ownerRegistration->created_at?->diffForHumans() ?? 'recently' }}</p>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('platform-admin.owner-registrations.approve', $ownerRegistration) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-500">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('platform-admin.owner-registrations.reject', $ownerRegistration) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center rounded-full border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition hover:border-red-300 hover:bg-red-100">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-slate-500">
                            No pending shop owner registrations.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Recent decisions</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">Approved and rejected requests</h2>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reviewed by</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Reviewed at</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($reviewedOwnerRegistrations as $ownerRegistration)
                                <tr>
                                    <td class="px-4 py-4 text-sm font-medium text-slate-900">{{ $ownerRegistration->name }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $ownerRegistration->email }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $ownerRegistration->owner_registration_status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $ownerRegistration->owner_registration_status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $ownerRegistration->approvedBy?->name ?? 'System' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $ownerRegistration->owner_registration_reviewed_at?->format('M d, Y h:i A') ?? 'Not recorded' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No reviewed registrations yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Audit log</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">Decision history</h2>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Shop owner</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Previous status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">New status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Platform admin</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Logged at</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($ownerRegistrationReviews as $review)
                                <tr>
                                    <td class="px-4 py-4 text-sm font-medium text-slate-900">{{ $review->shopOwner?->name ?? 'Unknown user' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ ucfirst($review->action) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $review->previous_status ?? 'none' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $review->new_status }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $review->platformAdmin?->name ?? 'Unknown admin' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-600">{{ $review->created_at?->format('M d, Y h:i A') ?? 'Not recorded' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No audit entries recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>