<x-app-layout>
    <div class="admin-page">
        <div class="admin-page-container">
            <!-- Admin hero explains that this screen gates owner access to the business workspace. -->
            <div class="admin-hero">
                <p class="admin-eyebrow">Platform Admin</p>
                <h1 class="admin-hero-title">Shop owner approval queue</h1>
                <p class="admin-hero-copy">Review new shop owner registrations and approve or reject them before they can access their shop workspace.</p>
            </div>

            @if (session('success'))
                <div class="admin-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="admin-stack">
                <!-- Pending queue is the actionable review surface for new owner registrations. -->
                <section class="admin-panel">
                    <div class="admin-panel-header">
                        <div>
                            <p class="profile-eyebrow">Pending approvals</p>
                            <h2 class="admin-panel-title">Review incoming shop owner requests</h2>
                        </div>
                        <span class="admin-badge">{{ $pendingOwnerRegistrations->count() }} pending</span>
                    </div>

                    <div class="admin-card-list">
                        @forelse ($pendingOwnerRegistrations as $ownerRegistration)
                            <article class="admin-card">
                                <div class="admin-card-row lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <h3 class="admin-card-title">{{ $ownerRegistration->name }}</h3>
                                        <p class="admin-card-copy">{{ $ownerRegistration->email }}</p>
                                        <p class="admin-card-meta">First shop: <span class="font-medium">{{ $ownerRegistration->pending_shop_name ?? 'Not provided' }}</span></p>
                                        <p class="admin-card-subtle">{{ $ownerRegistration->pending_shop_address ?? 'No address submitted' }}</p>
                                        <p class="admin-card-subtle">Registered {{ $ownerRegistration->created_at?->diffForHumans() ?? 'recently' }}</p>
                                    </div>

                                    <div class="admin-card-actions">
                                        <form method="POST" action="{{ route('platform-admin.owner-registrations.approve', $ownerRegistration) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="admin-button admin-button--approve">
                                                Approve
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('platform-admin.owner-registrations.reject', $ownerRegistration) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="admin-button admin-button--reject">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty-state">
                                No pending shop owner registrations.
                            </div>
                        @endforelse
                    </div>
                </section>

                <!-- Reviewed registrations table gives a quick snapshot of recent outcomes. -->
                <section class="admin-panel">
                    <p class="profile-eyebrow">Recent decisions</p>
                    <h2 class="admin-panel-title">Approved and rejected requests</h2>

                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Reviewed by</th>
                                    <th>Reviewed at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviewedOwnerRegistrations as $ownerRegistration)
                                    <tr>
                                        <td class="admin-table-strong">{{ $ownerRegistration->name }}</td>
                                        <td>{{ $ownerRegistration->email }}</td>
                                        <td>
                                            <span class="admin-status-badge {{ $ownerRegistration->owner_registration_status === 'approved' ? 'admin-status-badge--approved' : 'admin-status-badge--rejected' }}">
                                                {{ $ownerRegistration->owner_registration_status }}
                                            </span>
                                        </td>
                                        <td>{{ $ownerRegistration->approvedBy?->name ?? 'System' }}</td>
                                        <td>{{ $ownerRegistration->owner_registration_reviewed_at?->format('M d, Y h:i A') ?? 'Not recorded' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No reviewed registrations yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Audit table shows the underlying approval log written for each decision. -->
                <section class="admin-panel">
                    <p class="profile-eyebrow">Audit log</p>
                    <h2 class="admin-panel-title">Decision history</h2>

                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Shop owner</th>
                                    <th>Action</th>
                                    <th>Previous status</th>
                                    <th>New status</th>
                                    <th>Platform admin</th>
                                    <th>Logged at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ownerRegistrationReviews as $review)
                                    <tr>
                                        <td class="admin-table-strong">{{ $review->shopOwner?->name ?? 'Unknown user' }}</td>
                                        <td>{{ ucfirst($review->action) }}</td>
                                        <td>{{ $review->previous_status ?? 'none' }}</td>
                                        <td>{{ $review->new_status }}</td>
                                        <td>{{ $review->platformAdmin?->name ?? 'Unknown admin' }}</td>
                                        <td>{{ $review->created_at?->format('M d, Y h:i A') ?? 'Not recorded' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No audit entries recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>