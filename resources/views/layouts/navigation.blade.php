@php
    // Collapse account state into simple booleans so the menu can switch surfaces cleanly.
    $isAuthenticated = auth()->check();
    $isPlatformAdmin = $isAuthenticated && auth()->user()->is_platform_admin;
    $isShopOwner = $isAuthenticated && ! $isPlatformAdmin && (
        auth()->user()->shops()->exists() || auth()->user()->isApprovedShopOwnerRegistration()
    );
    $isCustomer = $isAuthenticated && ! $isPlatformAdmin && ! $isShopOwner;
@endphp

<nav x-data="navigationMenu()" class="app-nav border-b">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-neutral-950" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if (! $isPlatformAdmin && ! $isShopOwner)
                        <x-nav-link :href="route('customer.shops.index')" :active="request()->routeIs('customer.shops.*') || request()->routeIs('customer.orders.create')">
                            {{ __('Browse Shops') }}
                        </x-nav-link>
                    @endif

                    @if ($isPlatformAdmin)
                        <x-nav-link :href="route('platform-admin.owner-registrations.index')" :active="request()->routeIs('platform-admin.owner-registrations.*') || request()->routeIs('dashboard')">
                            {{ __('Owner Approvals') }}
                        </x-nav-link>
                    @elseif ($isShopOwner)
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('services.*') || request()->routeIs('orders.*') || request()->routeIs('shops.*')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-4">
                @guest
                    <!-- Guests get direct entry points into each portal. -->
                    <a href="{{ route('customer.login') }}" class="inline-flex items-center rounded-full border border-neutral-900/15 bg-white px-4 py-2 text-sm font-medium text-neutral-950 transition hover:border-neutral-950 hover:bg-neutral-950 hover:text-white">Customer Login</a>
                    <a href="{{ route('admin.login') }}" class="inline-flex items-center rounded-full border border-neutral-900/15 bg-white px-4 py-2 text-sm font-medium text-neutral-950 transition hover:border-neutral-950 hover:bg-neutral-950 hover:text-white">Shop Owner Login</a>
                    <a href="{{ route('platform-admin.login') }}" class="inline-flex items-center rounded-full border border-neutral-900/15 bg-white px-4 py-2 text-sm font-medium text-neutral-950 transition hover:border-neutral-950 hover:bg-neutral-950 hover:text-white">Admin Login</a>
                @endguest

                @auth
                    @if ($isCustomer)
                        <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center rounded-full border border-neutral-200 bg-white/80 px-4 py-2 text-sm font-medium text-neutral-950 transition hover:bg-neutral-100 hover:text-neutral-950">
                            Order history
                        </a>
                    @endif

                    <!-- Signed-in users get a shared account dropdown with role-specific shortcuts. -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center rounded-full border border-neutral-200 bg-white/80 px-3 py-2 text-sm font-medium leading-4 text-neutral-950 hover:bg-neutral-100 hover:text-neutral-950 focus:outline-none focus:bg-neutral-100 transition ease-in-out duration-150">
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

                                <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-neutral-950 hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 transition duration-150 ease-in-out">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="toggle()" class="inline-flex items-center justify-center rounded-2xl p-2 text-neutral-700 hover:bg-neutral-100 hover:text-neutral-950 focus:outline-none focus:bg-neutral-100 focus:text-neutral-950 transition duration-150 ease-in-out">
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
        <!-- Mobile menu mirrors the desktop links using the same role checks above. -->
        <div class="pt-2 pb-3 space-y-1">
            @if (! $isPlatformAdmin && ! $isShopOwner)
                <x-responsive-nav-link :href="route('customer.shops.index')" :active="request()->routeIs('customer.shops.*') || request()->routeIs('customer.orders.create')">
                    {{ __('Browse Shops') }}
                </x-responsive-nav-link>
            @endif

            @if ($isPlatformAdmin)
                <x-responsive-nav-link :href="route('platform-admin.owner-registrations.index')" :active="request()->routeIs('platform-admin.owner-registrations.*') || request()->routeIs('dashboard')">
                    {{ __('Owner Approvals') }}
                </x-responsive-nav-link>
            @elseif ($isShopOwner)
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('services.*') || request()->routeIs('orders.*') || request()->routeIs('shops.*')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-neutral-200 pt-4 pb-1">
            @auth
                <div class="px-4">
                    <div class="text-base font-medium text-neutral-950">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-neutral-600">{{ Auth::user()->email }}</div>
                </div>
            @else
                <div class="px-4 space-y-1">
                    <a href="{{ route('customer.login') }}" class="block text-sm font-medium text-neutral-700 hover:text-neutral-950">Customer Login</a>
                    <a href="{{ route('admin.login') }}" class="block text-sm font-medium text-neutral-700 hover:text-neutral-950">Shop Owner Login</a>
                    <a href="{{ route('platform-admin.login') }}" class="block text-sm font-medium text-neutral-700 hover:text-neutral-950">Admin Login</a>
                </div>
            @endauth

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

                        <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-neutral-700 hover:text-neutral-950 hover:bg-neutral-100 hover:border-neutral-300 focus:outline-none focus:text-neutral-950 focus:bg-neutral-100 focus:border-neutral-400 transition duration-150 ease-in-out">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
