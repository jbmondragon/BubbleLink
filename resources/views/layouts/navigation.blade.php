@php
    $isAuthenticated = auth()->check();
    $organizationMemberships = auth()->check()
        ? auth()->user()->memberships()->with(['organization', 'shop'])->orderByRaw("case role when 'owner' then 1 when 'manager' then 2 when 'staff' then 3 else 4 end")->get()
        : collect();
    $isPlatformAdmin = $isAuthenticated && auth()->user()->is_platform_admin;
    $isCustomer = $isAuthenticated && ! $isPlatformAdmin && $organizationMemberships->isEmpty();

    $activeOrganizationId = session('current_organization_id');

    if ($activeOrganizationId === null && $organizationMemberships->isNotEmpty()) {
        $activeOrganizationId = $organizationMemberships->first()->organization_id;
    }

    $activeOrganizationMembership = $organizationMemberships->firstWhere('organization_id', (int) $activeOrganizationId);
@endphp

<nav x-data="{ open: false }" class="app-nav border-b">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-teal-900" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (! $isPlatformAdmin && ! $isCustomer)
                        <x-nav-link :href="route('customer.shops.index')" :active="request()->routeIs('customer.shops.*') || request()->routeIs('customer.orders.create')">
                            {{ __('Browse Shops') }}
                        </x-nav-link>
                    @endif

                    @if ($isPlatformAdmin)
                        <x-nav-link :href="route('platform-admin.owner-registrations.index')" :active="request()->routeIs('platform-admin.owner-registrations.*') || request()->routeIs('dashboard')">
                            {{ __('Owner Approvals') }}
                        </x-nav-link>
                    @elseif ($organizationMemberships->isNotEmpty())
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('services.*') || request()->routeIs('orders.*') || request()->routeIs('memberships.*') || request()->routeIs('shops.*') || request()->routeIs('organizations.*')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-4">
                @guest
                    <a href="{{ route('customer.login') }}" class="inline-flex items-center rounded-full border border-teal-900/15 bg-white px-4 py-2 text-sm font-medium text-teal-900 transition hover:border-teal-900 hover:bg-teal-900 hover:text-amber-50">Customer Login</a>
                    <a href="{{ route('admin.login') }}" class="inline-flex items-center rounded-full border border-teal-900/15 bg-white px-4 py-2 text-sm font-medium text-teal-900 transition hover:border-teal-900 hover:bg-teal-900 hover:text-amber-50">Shop Owner Login</a>
                    <a href="{{ route('platform-admin.login') }}" class="inline-flex items-center rounded-full border border-teal-900/15 bg-white px-4 py-2 text-sm font-medium text-teal-900 transition hover:border-teal-900 hover:bg-teal-900 hover:text-amber-50">Admin Login</a>
                @endguest

                @if ($organizationMemberships->count() > 1)
                    <form method="POST" action="{{ route('organizations.switch') }}" class="flex items-center gap-2">
                        @csrf
                        <label for="navigation_organization_id" class="text-xs font-semibold uppercase tracking-wide text-teal-800/70">Organization</label>
                        <select id="navigation_organization_id" name="organization_id" onchange="this.form.submit()" class="rounded-2xl border-orange-200 bg-white/90 py-1 text-sm text-teal-950 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                            @foreach ($organizationMemberships as $membership)
                                <option value="{{ $membership->organization_id }}" @selected($membership->organization_id === (int) $activeOrganizationId)>
                                    {{ $membership->organization->name }} ({{ ucfirst($membership->role) }}{{ $membership->shop ? ' · '.$membership->shop->shop_name : '' }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @elseif ($activeOrganizationMembership)
                    <div class="text-sm text-teal-800/80">
                        <span class="font-medium text-teal-950">{{ $activeOrganizationMembership->organization->name }}</span>
                        <span class="text-xs uppercase tracking-wide text-orange-700">{{ $activeOrganizationMembership->role }}</span>
                        @if ($activeOrganizationMembership->shop)
                            <div class="mt-1 text-xs text-teal-800/70">Assigned shop: <span class="font-medium text-teal-950">{{ $activeOrganizationMembership->shop->shop_name }}</span></div>
                        @endif
                    </div>
                @endif

                @auth
                    <!-- Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center rounded-full border border-orange-100 bg-white/80 px-3 py-2 text-sm font-medium leading-4 text-teal-900 hover:text-teal-950 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if ($isPlatformAdmin)
                                <x-dropdown-link :href="route('platform-admin.owner-registrations.index')">
                                    {{ __('Owner Approvals') }}
                                </x-dropdown-link>
                            @elseif ($isCustomer)
                                <x-dropdown-link :href="route('customer.orders.index')">
                                    {{ __('My Orders') }}
                                </x-dropdown-link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-2xl p-2 text-teal-800 hover:bg-white/70 hover:text-teal-950 focus:outline-none focus:bg-white/70 focus:text-teal-950 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if (! $isPlatformAdmin && ! $isCustomer)
                <x-responsive-nav-link :href="route('customer.shops.index')" :active="request()->routeIs('customer.shops.*') || request()->routeIs('customer.orders.create')">
                    {{ __('Browse Shops') }}
                </x-responsive-nav-link>
            @endif

            @if ($isPlatformAdmin)
                <x-responsive-nav-link :href="route('platform-admin.owner-registrations.index')" :active="request()->routeIs('platform-admin.owner-registrations.*') || request()->routeIs('dashboard')">
                    {{ __('Owner Approvals') }}
                </x-responsive-nav-link>
            @elseif ($organizationMemberships->isNotEmpty())
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('services.*') || request()->routeIs('orders.*') || request()->routeIs('memberships.*') || request()->routeIs('shops.*') || request()->routeIs('organizations.*')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-orange-100 pt-4 pb-1">
            @auth
                <div class="px-4">
                    <div class="text-base font-medium text-teal-950">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-teal-800/80">{{ Auth::user()->email }}</div>
                    @if ($activeOrganizationMembership?->shop)
                        <div class="mt-1 text-xs font-medium uppercase tracking-wide text-orange-700">Assigned shop: {{ $activeOrganizationMembership->shop->shop_name }}</div>
                    @endif
                </div>
            @else
                <div class="px-4 space-y-1">
                    <a href="{{ route('customer.login') }}" class="block text-sm font-medium text-teal-800 hover:text-teal-950">Customer Login</a>
                    <a href="{{ route('admin.login') }}" class="block text-sm font-medium text-teal-800 hover:text-teal-950">Shop Owner Login</a>
                    <a href="{{ route('platform-admin.login') }}" class="block text-sm font-medium text-teal-800 hover:text-teal-950">Admin Login</a>
                </div>
            @endauth

            @if ($organizationMemberships->isNotEmpty())
                <div class="mt-3 px-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-teal-800/70">Organization</div>

                    @if ($organizationMemberships->count() > 1)
                        <form method="POST" action="{{ route('organizations.switch') }}" class="mt-2">
                            @csrf
                            <select name="organization_id" onchange="this.form.submit()" class="block w-full rounded-2xl border-orange-200 bg-white/90 text-sm text-teal-950 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                                @foreach ($organizationMemberships as $membership)
                                    <option value="{{ $membership->organization_id }}" @selected($membership->organization_id === (int) $activeOrganizationId)>
                                        {{ $membership->organization->name }} ({{ ucfirst($membership->role) }}{{ $membership->shop ? ' · '.$membership->shop->shop_name : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @elseif ($activeOrganizationMembership)
                        <div class="mt-2 text-sm text-teal-800/80">
                            {{ $activeOrganizationMembership->organization->name }} ({{ ucfirst($activeOrganizationMembership->role) }})
                            @if ($activeOrganizationMembership->shop)
                                <div class="mt-1 text-xs text-teal-800/70">Assigned shop: {{ $activeOrganizationMembership->shop->shop_name }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            @auth
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if ($isPlatformAdmin)
                        <x-responsive-nav-link :href="route('platform-admin.owner-registrations.index')">
                            {{ __('Owner Approvals') }}
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
