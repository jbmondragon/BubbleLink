<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-600">Laundry Marketplace</p>
                <h1 class="text-3xl font-semibold text-slate-900">Find a shop near you</h1>
                <p class="mt-1 text-sm text-teal-900/70">Browse available branches, compare services, and place your order online.</p>
            </div>
            @auth
                @if (auth()->user()->memberships()->doesntExist())
                    <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center rounded-full border border-orange-200 bg-white/80 px-4 py-2 text-sm font-medium text-teal-900 hover:border-orange-300 hover:text-teal-950">
                        View my orders
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="app-hero overflow-hidden rounded-3xl px-6 py-8 text-white sm:px-8">
                <div class="grid gap-6 lg:grid-cols-[1.4fr_0.8fr] lg:items-end">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-amber-100">Fresh pickup and delivery</p>
                        <h2 class="mt-3 text-4xl font-semibold leading-tight">Book laundry services without calling every branch.</h2>
                        <p class="mt-3 max-w-2xl text-sm text-orange-50/90">Search the catalog, open a shop page, review pricing, then place your order using your BubbleLink account.</p>

                        @guest
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('customer.login') }}" class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-teal-950 transition hover:bg-orange-50">
                                    Customer Login
                                </a>
                                <a href="{{ route('customer.register') }}" class="inline-flex items-center rounded-full border border-white/40 px-5 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10">
                                    Customer Register
                                </a>
                                <a href="{{ route('admin.login') }}" class="inline-flex items-center rounded-full bg-teal-950/70 px-5 py-3 text-sm font-semibold text-orange-50 transition hover:bg-teal-950">
                                    Admin Login
                                </a>
                                <a href="{{ route('admin.register') }}" class="inline-flex items-center rounded-full border border-orange-100/50 px-5 py-3 text-sm font-semibold text-orange-50 transition hover:border-orange-50 hover:bg-white/10">
                                    Admin Register
                                </a>
                            </div>
                        @endguest
                    </div>

                    <form method="GET" action="{{ route('customer.shops.index') }}" class="rounded-3xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                        <label for="search" class="block text-sm font-medium text-orange-50">Search shops</label>
                        <div class="mt-3 flex gap-3">
                            <input id="search" name="search" value="{{ $search }}" placeholder="Search by branch, address, or description" class="w-full rounded-2xl border border-white/20 bg-white/90 px-4 py-3 text-sm text-slate-900 placeholder:text-slate-500 focus:border-white focus:outline-none focus:ring-2 focus:ring-white/40">
                            <button type="submit" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-teal-950 transition hover:bg-orange-50">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            @guest
                <section class="app-panel mt-8 rounded-3xl p-6 sm:p-8">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Seeded Demo Accounts</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Quick login references for local testing</h2>
                            <p class="mt-2 text-sm text-teal-900/70">These credentials match the seeded local demo data so you can test customer and admin flows immediately.</p>
                        </div>
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-orange-700">Local demo only</p>
                    </div>

                    <div class="mt-6 grid gap-6 lg:grid-cols-2">
                        <div class="app-panel-muted rounded-3xl p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Customer Accounts</p>
                            <div class="mt-4 space-y-4">
                                @foreach ($demoAccounts['customers'] as $account)
                                    <article x-data="{ copied: false, async copyCredentials() { await navigator.clipboard.writeText('Email: {{ $account['email'] }} | Password: {{ $account['password'] }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }" class="rounded-2xl border border-orange-100 bg-white/90 p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-slate-900">{{ $account['label'] }}</h3>
                                        <p class="mt-1 text-sm text-teal-900/70">{{ $account['description'] }}</p>
                                        <dl class="mt-3 space-y-1 text-sm">
                                            <div><span class="font-semibold text-slate-900">Email:</span> <span class="text-slate-700">{{ $account['email'] }}</span></div>
                                            <div><span class="font-semibold text-slate-900">Password:</span> <span class="text-slate-700">{{ $account['password'] }}</span></div>
                                        </dl>
                                        <button type="button" x-on:click="copyCredentials()" class="mt-4 inline-flex items-center rounded-full border border-orange-200 bg-orange-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-orange-800 transition hover:border-orange-300 hover:bg-orange-100">
                                            <span x-show="! copied">Copy credentials</span>
                                            <span x-show="copied" x-cloak>Copied</span>
                                        </button>
                                        <form method="POST" action="{{ route('customer.login.store') }}" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="email" value="{{ $account['email'] }}">
                                            <input type="hidden" name="password" value="{{ $account['password'] }}">
                                            <button type="submit" class="inline-flex items-center rounded-full bg-teal-900 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-orange-50 transition hover:bg-teal-800">
                                                One-click customer login
                                            </button>
                                        </form>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-3xl border border-teal-900/10 bg-gradient-to-br from-teal-950 via-teal-900 to-orange-700 p-6 text-white shadow-lg">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-100">Admin Accounts</p>
                            <div class="mt-4 space-y-4">
                                @foreach ($demoAccounts['admins'] as $account)
                                    <article x-data="{ copied: false, async copyCredentials() { await navigator.clipboard.writeText('Email: {{ $account['email'] }} | Password: {{ $account['password'] }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }" class="rounded-2xl border border-white/10 bg-white/8 p-4 backdrop-blur-sm">
                                        <h3 class="text-lg font-semibold text-white">{{ $account['label'] }}</h3>
                                        <p class="mt-1 text-sm text-slate-200">{{ $account['description'] }}</p>
                                        <dl class="mt-3 space-y-1 text-sm">
                                            <div><span class="font-semibold text-white">Email:</span> <span class="text-slate-100">{{ $account['email'] }}</span></div>
                                            <div><span class="font-semibold text-white">Password:</span> <span class="text-slate-100">{{ $account['password'] }}</span></div>
                                        </dl>
                                        <button type="button" x-on:click="copyCredentials()" class="mt-4 inline-flex items-center rounded-full border border-orange-100/40 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-orange-50 transition hover:border-orange-50/70 hover:bg-white/15">
                                            <span x-show="! copied">Copy credentials</span>
                                            <span x-show="copied" x-cloak>Copied</span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="email" value="{{ $account['email'] }}">
                                            <input type="hidden" name="password" value="{{ $account['password'] }}">
                                            <button type="submit" class="inline-flex items-center rounded-full bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-teal-950 transition hover:bg-orange-50">
                                                One-click admin login
                                            </button>
                                        </form>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endguest

            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($shops as $shop)
                    <article class="app-panel flex h-full flex-col justify-between rounded-3xl p-6 transition hover:-translate-y-1 hover:shadow-xl">
                        <div>
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">{{ $shop->organization->name }}</p>
                                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $shop->shop_name }}</h2>
                                </div>
                                <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">{{ $shop->shopServices->count() }} services</span>
                            </div>

                            <p class="mt-4 text-sm text-teal-900/70">{{ $shop->description ?: 'Trusted laundry branch with pickup, drop-off, and delivery options.' }}</p>

                            <dl class="mt-6 space-y-3 text-sm text-teal-900/70">
                                <div>
                                    <dt class="font-semibold text-slate-900">Address</dt>
                                    <dd>{{ $shop->address }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-slate-900">Contact</dt>
                                    <dd>{{ $shop->contact_number ?: 'Contact details available on request' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-slate-900">Popular services</dt>
                                    <dd class="mt-1 flex flex-wrap gap-2">
                                        @forelse ($shop->shopServices->take(3) as $shopService)
                                            <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800">{{ $shopService->service->name }}</span>
                                        @empty
                                            <span class="text-xs text-slate-500">No services listed yet</span>
                                        @endforelse
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-4 border-t border-orange-100 pt-5">
                            <span class="text-sm text-teal-800/80">Starting at <span class="font-semibold text-slate-900">PHP {{ number_format((float) $shop->shopServices->min('price'), 2) }}</span></span>
                            <a href="{{ route('customer.shops.show', $shop) }}" class="inline-flex items-center rounded-full bg-teal-900 px-4 py-2 text-sm font-semibold text-orange-50 transition hover:bg-teal-800">View details</a>
                        </div>
                    </article>
                @empty
                    <div class="app-panel rounded-3xl border-dashed p-10 text-center text-teal-800/70 md:col-span-2 xl:col-span-3">
                        No shops matched your search.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>