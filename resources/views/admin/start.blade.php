<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="app-hero overflow-hidden rounded-3xl px-8 py-10 text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-100">Admin Workspace</p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight">Set up your organization before you manage shops and orders.</h1>
                <p class="mt-4 max-w-2xl text-sm text-orange-50/90">This onboarding page is for new admin accounts with no organization yet. Create your organization first, then you will land on the dashboard and unlock the full management tools.</p>
            </div>

            @if (session('warning'))
                <div class="mt-6 rounded-2xl border border-orange-200 bg-orange-50 px-5 py-4 text-orange-900">
                    {{ session('warning') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mt-6 rounded-2xl border border-teal-200 bg-teal-50 px-5 py-4 text-teal-900">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="app-panel rounded-3xl p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Next Step</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">Create your organization</h2>
                    <p class="mt-3 text-sm text-teal-900/70">This creates your first organization and links your account as the owner so you can manage services, memberships, shops, and orders.</p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('organizations.create', ['guided' => 1]) }}" class="inline-flex items-center rounded-full bg-teal-900 px-5 py-3 text-sm font-semibold text-orange-50 transition hover:bg-teal-800">
                            Create organization
                        </a>
                        <a href="{{ route('customer.shops.index') }}" class="inline-flex items-center rounded-full border border-orange-200 bg-white/80 px-5 py-3 text-sm font-semibold text-teal-900 transition hover:border-orange-300 hover:text-teal-950">
                            Browse public catalog
                        </a>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="app-panel rounded-3xl p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Onboarding Checklist</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Launch your admin workspace in three steps</h2>
                        <div class="mt-6 space-y-4">
                            <article class="rounded-2xl border border-orange-200 bg-orange-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-700">Step 1</p>
                                <h3 class="mt-2 text-lg font-semibold text-orange-950">Create organization</h3>
                                <p class="mt-1 text-sm text-orange-900">Set up your first organization so your account becomes the owner and unlocks the guided setup wizard.</p>
                            </article>
                            <article class="rounded-2xl border border-teal-200 bg-teal-50/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-700">Step 2</p>
                                <h3 class="mt-2 text-lg font-semibold text-slate-900">Add your first shop</h3>
                                <p class="mt-1 text-sm text-teal-900/70">After organization setup, open the dashboard and create your first branch with address and contact details.</p>
                            </article>
                            <article class="rounded-2xl border border-teal-200 bg-teal-50/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-700">Step 3</p>
                                <h3 class="mt-2 text-lg font-semibold text-slate-900">Publish services and start orders</h3>
                                <p class="mt-1 text-sm text-teal-900/70">Create service types, assign prices to shops, and start accepting customer orders from the public catalog.</p>
                            </article>
                        </div>
                    </section>

                    <section class="app-panel-muted rounded-3xl p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">What unlocks after setup</p>
                        <ol class="mt-4 space-y-3 text-sm text-teal-900/70">
                            <li>1. Dashboard metrics for your organization.</li>
                            <li>2. Shop, service, and membership management pages.</li>
                            <li>3. Internal order management for staff and owners.</li>
                            <li>4. Multi-organization switching if you join more than one team.</li>
                        </ol>
                    </section>

                    <section class="app-panel rounded-3xl p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Current account</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ auth()->user()->name }}</h2>
                        <p class="mt-2 text-sm text-teal-900/70">{{ auth()->user()->email }}</p>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>