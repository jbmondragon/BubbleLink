<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="app-hero overflow-hidden rounded-3xl px-8 py-10 text-white">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-100">Guided Setup</p>
                <h1 class="mt-3 text-4xl font-semibold leading-tight">Finish your first admin setup for {{ $organization->name }}.</h1>
                <p class="mt-4 max-w-2xl text-sm text-orange-50/90">Use this guided wizard to create the minimum setup your team needs: one shop, one service, and one teammate.</p>
            </div>

            @if (session('success'))
                <div class="mt-6 rounded-2xl border border-teal-200 bg-teal-50 px-5 py-4 text-teal-900">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="space-y-6">
                    <article class="rounded-3xl border p-8 shadow-sm {{ $hasShop ? 'border-orange-200 bg-orange-50' : 'app-panel' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $hasShop ? 'text-orange-700' : 'text-orange-600' }}">Step 1</p>
                                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Create your first shop</h2>
                                <p class="mt-2 text-sm text-teal-900/70">Add a branch with address and contact details so customers can place orders.</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $hasShop ? 'bg-orange-200 text-orange-900' : 'bg-teal-50 text-teal-800' }}">
                                {{ $hasShop ? 'Done' : 'Pending' }}
                            </span>
                        </div>

                        @if ($hasShop)
                            <p class="mt-4 text-sm text-orange-900">Created shop: <span class="font-semibold">{{ $shops->first()->shop_name }}</span></p>
                        @else
                            <form method="POST" action="{{ route('admin.setup.shop') }}" class="mt-6 space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="setup_shop_name" value="Shop Name" />
                                    <x-text-input id="setup_shop_name" name="shop_name" class="mt-1 w-full" :value="old('shop_name')" required />
                                    <x-input-error :messages="$errors->getBag('setupShop')->get('shop_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_shop_address" value="Address" />
                                    <x-text-input id="setup_shop_address" name="address" class="mt-1 w-full" :value="old('address')" required />
                                    <x-input-error :messages="$errors->getBag('setupShop')->get('address')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_shop_contact" value="Contact Number" />
                                    <x-text-input id="setup_shop_contact" name="contact_number" class="mt-1 w-full" :value="old('contact_number')" />
                                    <x-input-error :messages="$errors->getBag('setupShop')->get('contact_number')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_shop_description" value="Description" />
                                    <x-text-input id="setup_shop_description" name="description" class="mt-1 w-full" :value="old('description')" />
                                    <x-input-error :messages="$errors->getBag('setupShop')->get('description')" class="mt-2" />
                                </div>
                                <x-primary-button>Create first shop</x-primary-button>
                            </form>
                        @endif
                    </article>

                    <article class="rounded-3xl border p-8 shadow-sm {{ $hasService ? 'border-orange-200 bg-orange-50' : 'app-panel' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $hasService ? 'text-orange-700' : 'text-orange-600' }}">Step 2</p>
                                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Create your first service</h2>
                                <p class="mt-2 text-sm text-teal-900/70">Add a service type and assign its first price to one of your shops.</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $hasService ? 'bg-orange-200 text-orange-900' : 'bg-teal-50 text-teal-800' }}">
                                {{ $hasService ? 'Done' : 'Pending' }}
                            </span>
                        </div>

                        @if ($hasService)
                            <p class="mt-4 text-sm text-orange-900">Created service: <span class="font-semibold">{{ $services->first()->name }}</span></p>
                        @elseif (! $hasShop)
                            <p class="mt-4 text-sm text-teal-800/70">Create a shop first to unlock this step.</p>
                        @else
                            <form method="POST" action="{{ route('admin.setup.service') }}" class="mt-6 space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="setup_service_shop" value="Assign to Shop" />
                                    <select id="setup_service_shop" name="shop_id" class="mt-1 block w-full rounded-2xl border-orange-200 bg-white/90 text-teal-950 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                                        @foreach ($shops as $shop)
                                            <option value="{{ $shop->id }}" @selected(old('shop_id') == $shop->id)>{{ $shop->shop_name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->getBag('setupService')->get('shop_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_service_name" value="Service Name" />
                                    <x-text-input id="setup_service_name" name="name" class="mt-1 w-full" :value="old('name')" required />
                                    <x-input-error :messages="$errors->getBag('setupService')->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_service_price" value="Starting Price" />
                                    <x-text-input id="setup_service_price" name="price" type="number" min="0.01" step="0.01" class="mt-1 w-full" :value="old('price')" required />
                                    <x-input-error :messages="$errors->getBag('setupService')->get('price')" class="mt-2" />
                                </div>
                                <x-primary-button>Create first service</x-primary-button>
                            </form>
                        @endif
                    </article>

                    <article class="rounded-3xl border p-8 shadow-sm {{ $hasExtraMember ? 'border-orange-200 bg-orange-50' : 'app-panel' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] {{ $hasExtraMember ? 'text-orange-700' : 'text-orange-600' }}">Step 3</p>
                                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Add your first teammate</h2>
                                <p class="mt-2 text-sm text-teal-900/70">Invite a manager or staff member so your team can start handling operations.</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $hasExtraMember ? 'bg-orange-200 text-orange-900' : 'bg-teal-50 text-teal-800' }}">
                                {{ $hasExtraMember ? 'Done' : 'Pending' }}
                            </span>
                        </div>

                        @if ($hasExtraMember)
                            <p class="mt-4 text-sm text-orange-900">Team size: <span class="font-semibold">{{ $memberships->count() }}</span> members</p>
                        @else
                            <form method="POST" action="{{ route('admin.setup.member') }}" class="mt-6 space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="setup_member_name" value="Name" />
                                    <x-text-input id="setup_member_name" name="name" class="mt-1 w-full" :value="old('name')" required />
                                    <x-input-error :messages="$errors->getBag('setupMember')->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_member_email" value="Email" />
                                    <x-text-input id="setup_member_email" name="email" type="email" class="mt-1 w-full" :value="old('email')" required />
                                    <x-input-error :messages="$errors->getBag('setupMember')->get('email')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_member_contact" value="Contact Number" />
                                    <x-text-input id="setup_member_contact" name="contact_number" class="mt-1 w-full" :value="old('contact_number')" />
                                    <x-input-error :messages="$errors->getBag('setupMember')->get('contact_number')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_member_role" value="Role" />
                                    <select id="setup_member_role" name="role" class="mt-1 block w-full rounded-2xl border-orange-200 bg-white/90 text-teal-950 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                                        <option value="manager" @selected(old('role') === 'manager')>Manager</option>
                                        <option value="staff" @selected(old('role') === 'staff')>Staff</option>
                                    </select>
                                    <x-input-error :messages="$errors->getBag('setupMember')->get('role')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="setup_member_shop" value="Assign to Shop" />
                                    <select id="setup_member_shop" name="shop_id" class="mt-1 block w-full rounded-2xl border-orange-200 bg-white/90 text-teal-950 shadow-sm focus:border-teal-600 focus:ring-teal-600">
                                        <option value="">Select a shop</option>
                                        @foreach ($shops as $shop)
                                            <option value="{{ $shop->id }}" @selected(old('shop_id') == $shop->id)>{{ $shop->shop_name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->getBag('setupMember')->get('shop_id')" class="mt-2" />
                                </div>
                                <x-primary-button>Add first teammate</x-primary-button>
                            </form>
                        @endif
                    </article>
                </section>

                <aside class="space-y-6">
                    <section class="app-panel-muted rounded-3xl p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Progress</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Setup overview</h2>
                        <div class="mt-4 space-y-3 text-sm text-teal-900/70">
                            <p>Organization: <span class="font-semibold text-slate-900">{{ $organization->name }}</span></p>
                            <p>Shops: <span class="font-semibold text-slate-900">{{ $shops->count() }}</span></p>
                            <p>Services: <span class="font-semibold text-slate-900">{{ $services->count() }}</span></p>
                            <p>Team members: <span class="font-semibold text-slate-900">{{ $memberships->count() }}</span></p>
                        </div>
                    </section>

                    <section class="app-panel rounded-3xl p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-600">Next destination</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Open the dashboard anytime</h2>
                        <p class="mt-3 text-sm text-teal-900/70">You can leave the wizard at any point and continue setup from the standard management pages.</p>
                        <a href="{{ route('dashboard') }}" class="mt-5 inline-flex items-center rounded-full bg-teal-900 px-5 py-3 text-sm font-semibold text-orange-50 transition hover:bg-teal-800">
                            Go to dashboard
                        </a>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>