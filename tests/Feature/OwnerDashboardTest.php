<?php

use App\Models\Membership;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;

it('loads the owner dashboard with overview data and management links', function () {
    ['owner' => $owner, 'shop' => $shop, 'service' => $service, 'order' => $order] = createOwnerDashboardContext();

    $response = $this->actingAs($owner)->get(route('dashboard'));

    $response->assertOk()
        ->assertViewHas('shopCount', 1)
        ->assertViewHas('totalOrders', 1)
        ->assertViewHas('memberCount', 1)
        ->assertViewHas('totalRevenue', fn ($totalRevenue) => (float) $totalRevenue === 150.5)
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('Services')
        ->assertSeeText('Orders')
        ->assertSeeText('Members')
        ->assertSee(route('memberships.index'), false)
        ->assertDontSee(route('services.index'), false)
        ->assertDontSee(route('orders.index'), false)
        ->assertSee(route('shops.create'), false)
        ->assertSeeText('Owners can monitor order totals here, while managers and staff handle order updates.');
});

it('lets a manager create a service and assign it to a shop', function () {
    ['organization' => $organization, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);
    $manager = createUser();

    Membership::create([
        'user_id' => $manager->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    $this->actingAs($manager)
        ->post(route('services.store'), ['name' => 'Dry Cleaning'])
        ->assertRedirect(route('services.index'));

    $service = Service::query()
        ->where('name', 'Dry Cleaning')
        ->where('organization_id', $shop->organization_id)
        ->first();

    expect($service)->not->toBeNull();

    $this->actingAs($manager)
        ->post(route('shop-services.store'), [
            'shop_id' => $shop->id,
            'service_id' => $service->id,
            'price' => 220.75,
        ])
        ->assertRedirect(route('services.index'));

    $this->assertDatabaseHas('shop_services', [
        'shop_id' => $shop->id,
        'service_id' => $service->id,
        'price' => 220.75,
    ]);
});

it('does not show another organizations services on the manager services page', function () {
    ['organization' => $organization, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);
    ['organization' => $otherOrganization] = createOwnerDashboardContext(includeOrder: false, includeService: false);
    $manager = createUser();

    Membership::create([
        'user_id' => $manager->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    Service::create([
        'organization_id' => $organization->id,
        'name' => 'Owner Only Service',
    ]);

    Service::create([
        'organization_id' => $otherOrganization->id,
        'name' => 'Other Organization Service',
    ]);

    $this->actingAs($manager)
        ->get(route('services.index'))
        ->assertOk()
        ->assertSeeText('Assigned Shop')
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('Owner Only Service')
        ->assertDontSeeText('Other Organization Service');
});

it('lets a manager view the dedicated shop details but not the owner-only shop management pages', function () {
    ['organization' => $organization, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);
    $manager = createUser();

    Membership::create([
        'user_id' => $manager->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    $this->actingAs($manager)
        ->get(route('shops.show', $shop))
        ->assertOk()
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('Shop Workspace')
        ->assertSeeText('Shop Details');

    $this->actingAs($manager)
        ->get(route('shops.create'))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('shops.edit', $shop))
        ->assertForbidden();
});

it('lets an owner view the memberships and shop pages but not the service or orders management pages', function () {
    ['owner' => $owner] = createOwnerDashboardContext();

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('warning', 'Only managers can manage services. Owners can manage shops and member roles from the dashboard.');

    $this->actingAs($owner)
        ->get(route('orders.index'))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('warning', 'Only managers and staff can manage orders. Owners can view order totals from the dashboard.');

    $shop = Shop::query()->firstOrFail();

    $this->actingAs($owner)
        ->get(route('shops.show', $shop))
        ->assertOk();

    $this->actingAs($owner)
        ->get(route('memberships.index'))
        ->assertOk()
        ->assertSeeText('Current Members')
        ->assertDontSee(route('orders.index'), false);
});

it('redirects users without an organization to organization setup from dedicated management pages', function () {
    $user = createUser();

    $this->actingAs($user)
        ->get(route('services.index'))
        ->assertRedirect(route('organizations.create'))
        ->assertSessionHas('warning', 'Create your organization first to manage services.');

    $this->actingAs($user)
        ->get(route('orders.index'))
        ->assertRedirect(route('organizations.create'))
        ->assertSessionHas('warning', 'Create your organization first to manage orders.');

    $this->actingAs($user)
        ->get(route('memberships.index'))
        ->assertRedirect(route('organizations.create'))
        ->assertSessionHas('warning', 'Create your organization first to manage members.');
});

it('shows organization setup links on the dashboard when the user is not yet an owner', function () {
    $user = createUser();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('You have not created an organization yet.')
        ->assertSeeText('Create Organization')
        ->assertDontSeeText('+ Create Shop')
        ->assertSee(route('organizations.create'), false);
});

it('shows role-appropriate dashboard links for managers and staff', function () {
    ['organization' => $organization, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $managerUser = createUser();
    $staffUser = createUser();

    Membership::create([
        'user_id' => $managerUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    Membership::create([
        'user_id' => $staffUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'staff',
    ]);

    $this->actingAs($managerUser)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('Assigned Shop')
        ->assertSee(route('services.index'), false)
        ->assertSee(route('orders.index'), false)
        ->assertDontSee(route('memberships.index'), false)
        ->assertDontSeeText('+ Create Shop');

    $this->actingAs($staffUser)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('Assigned Shop')
        ->assertSee(route('orders.index'), false)
        ->assertDontSee(route('memberships.index'), false)
        ->assertDontSee(route('services.index'), false)
        ->assertDontSeeText('+ Create Shop');
});

it('lets a user switch the active organization they are managing', function () {
    ['owner' => $owner, 'organization' => $organization, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $secondOrganization = Organization::create([
        'name' => 'BubbleLink North',
        'owner_user_id' => createUser()->id,
    ]);

    $secondShop = Shop::create([
        'organization_id' => $secondOrganization->id,
        'shop_name' => 'BubbleLink North Hub',
        'address' => '987 North Street',
        'contact_number' => '09175551111',
        'description' => 'North branch',
    ]);

    Membership::create([
        'user_id' => $owner->id,
        'organization_id' => $secondOrganization->id,
        'shop_id' => $secondShop->id,
        'role' => 'manager',
    ]);

    Service::create([
        'organization_id' => $secondOrganization->id,
        'name' => 'North Branch Express',
    ]);

    $this->actingAs($owner)
        ->post(route('organizations.switch'), [
            'organization_id' => $secondOrganization->id,
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('current_organization_id', $secondOrganization->id);

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText($secondShop->shop_name)
        ->assertDontSeeText($shop->shop_name)
        ->assertDontSee(route('memberships.index'), false)
        ->assertSee(route('services.index'), false)
        ->assertSee(route('orders.index'), false)
        ->assertDontSeeText('+ Create Shop');

    $this->actingAs($owner)
        ->get(route('memberships.index'))
        ->assertForbidden();

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertOk();
});

it('forbids switching to an organization the user does not belong to', function () {
    ['owner' => $owner] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $otherOwner = createUser();

    $otherOrganization = Organization::create([
        'name' => 'Unauthorized Organization',
        'owner_user_id' => $otherOwner->id,
    ]);

    Membership::create([
        'user_id' => $otherOwner->id,
        'organization_id' => $otherOrganization->id,
        'role' => 'owner',
    ]);

    $this->actingAs($owner)
        ->post(route('organizations.switch'), [
            'organization_id' => $otherOrganization->id,
        ])
        ->assertForbidden();
});

it('shows the redirect warning on the organization setup page', function () {
    $user = createUser();

    $this->actingAs($user)
        ->followingRedirects()
        ->get(route('services.index'))
        ->assertOk()
        ->assertSeeText('Create your organization first to manage services.')
        ->assertSeeText('Create Organization');
});

it('shows the owner restriction warning on the dashboard when an owner opens services', function () {
    ['owner' => $owner] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $this->actingAs($owner)
        ->followingRedirects()
        ->get(route('services.index'))
        ->assertOk()
        ->assertSeeText('Only managers can manage services. Owners can manage shops and member roles from the dashboard.');
});

it('forbids staff from viewing the dedicated shop edit page', function () {
    ['organization' => $organization, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $staffUser = createUser();

    Membership::create([
        'user_id' => $staffUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'staff',
    ]);

    $this->actingAs($staffUser)
        ->get(route('shops.edit', $shop))
        ->assertForbidden();
});

it('lets organization staff view the dedicated shop details page', function () {
    ['organization' => $organization, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $staffUser = createUser();

    Membership::create([
        'user_id' => $staffUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'staff',
    ]);

    $this->actingAs($staffUser)
        ->get(route('shops.show', $shop))
        ->assertOk()
        ->assertSeeText($shop->shop_name);
});

it('forbids staff from viewing the dedicated shop create page', function () {
    ['organization' => $organization] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $staffUser = createUser();

    Membership::create([
        'user_id' => $staffUser->id,
        'organization_id' => $organization->id,
        'role' => 'staff',
    ]);

    $this->actingAs($staffUser)
        ->get(route('shops.create'))
        ->assertForbidden();
});

it('forbids an owner from updating an order status and payment state', function () {
    ['owner' => $owner, 'order' => $order] = createOwnerDashboardContext();

    $this->actingAs($owner)
        ->patch(route('orders.update', $order), [
            'order_id' => $order->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => $order->status,
        'payment_status' => $order->payment_status,
    ]);
});

it('forbids an owner from creating an order from the dedicated orders page', function () {
    ['owner' => $owner, 'shop' => $shop, 'shopService' => $shopService] = createOwnerDashboardContext(includeOrder: false);

    $this->actingAs($owner)
        ->post(route('orders.store'), [
            'customer_name' => 'Walk-in Customer',
            'customer_email' => 'walkin.customer@example.com',
            'customer_contact_number' => '09170001111',
            'shop_id' => $shop->id,
            'shop_service_id' => $shopService->id,
            'service_mode' => 'both',
            'pickup_address' => '123 Pickup Street',
            'delivery_address' => '789 Delivery Avenue',
            'weight' => 5.50,
            'pickup_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('warning', 'Only managers and staff can create orders. Owners can view order totals from the dashboard.');

    $this->assertDatabaseMissing('users', [
        'email' => 'walkin.customer@example.com',
    ]);
});

it('lets a manager filter orders on the dedicated orders page by shop, status, and payment status', function () {
    ['organization' => $organization, 'order' => $order] = createOwnerDashboardContext();

    $order->customer->update([
        'name' => 'Pending Customer',
        'email' => 'pending.customer@example.com',
    ]);

    $secondShop = Shop::create([
        'organization_id' => $organization->id,
        'shop_name' => 'BubbleLink East',
        'address' => '456 East Street',
        'contact_number' => '09173334444',
        'description' => 'Second branch',
    ]);

    $secondService = Service::create([
        'organization_id' => $organization->id,
        'name' => 'Express Wash',
    ]);

    $secondShopService = ShopService::create([
        'shop_id' => $secondShop->id,
        'service_id' => $secondService->id,
        'price' => 220.00,
    ]);

    $manager = createUser();

    Membership::create([
        'user_id' => $manager->id,
        'organization_id' => $organization->id,
        'shop_id' => $secondShop->id,
        'role' => 'manager',
    ]);

    $completedCustomer = User::create([
        'name' => 'Completed Customer',
        'email' => 'completed.customer@example.com',
        'password' => bcrypt('password'),
        'contact_number' => '09176667777',
    ]);

    Order::create([
        'customer_id' => $completedCustomer->id,
        'shop_id' => $secondShop->id,
        'shop_service_id' => $secondShopService->id,
        'service_mode' => 'delivery_only',
        'pickup_address' => null,
        'delivery_address' => '789 Delivery Road',
        'weight' => 3.75,
        'pickup_datetime' => null,
        'delivery_datetime' => now()->addDays(3),
        'total_price' => 220.00,
        'status' => 'completed',
        'payment_method' => 'gcash',
        'payment_status' => 'paid',
    ]);

    $this->actingAs($manager)
        ->get(route('orders.index', [
            'shop_id' => $secondShop->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]))
        ->assertOk()
        ->assertSeeText('Assigned Shop')
        ->assertSeeText($secondShop->shop_name)
        ->assertSeeText('Displayed Orders')
        ->assertSeeText('Completed Customer')
        ->assertDontSeeText('Pending Customer')
        ->assertSeeText('BubbleLink East')
        ->assertDontSeeText('Order #1');
});

it('lets a manager filter orders on the dedicated orders page by date range and see revenue totals', function () {
    ['organization' => $organization, 'order' => $order] = createOwnerDashboardContext();

    $order->customer->update([
        'name' => 'Older Customer',
        'email' => 'older.customer@example.com',
    ]);

    $order->forceFill([
        'created_at' => now()->subDays(10),
    ])->save();

    $secondShop = Shop::create([
        'organization_id' => $organization->id,
        'shop_name' => 'BubbleLink South',
        'address' => '890 South Street',
        'contact_number' => '09174445555',
        'description' => 'South branch',
    ]);

    $secondService = Service::create([
        'organization_id' => $organization->id,
        'name' => 'Premium Dry Clean',
    ]);

    $secondShopService = ShopService::create([
        'shop_id' => $secondShop->id,
        'service_id' => $secondService->id,
        'price' => 310.00,
    ]);

    $manager = createUser();

    Membership::create([
        'user_id' => $manager->id,
        'organization_id' => $organization->id,
        'shop_id' => $secondShop->id,
        'role' => 'manager',
    ]);

    $recentCustomer = User::create([
        'name' => 'Recent Customer',
        'email' => 'recent.customer@example.com',
        'password' => bcrypt('password'),
        'contact_number' => '09178889999',
    ]);

    $recentOrder = Order::create([
        'customer_id' => $recentCustomer->id,
        'shop_id' => $secondShop->id,
        'shop_service_id' => $secondShopService->id,
        'service_mode' => 'pickup_only',
        'pickup_address' => '987 Pickup Road',
        'delivery_address' => null,
        'weight' => 2.25,
        'pickup_datetime' => now()->addDay(),
        'delivery_datetime' => now()->addDays(2),
        'total_price' => 310.00,
        'status' => 'accepted',
        'payment_method' => 'gcash',
        'payment_status' => 'paid',
    ]);

    $recentOrder->forceFill([
        'created_at' => now()->subDay(),
    ])->save();

    $this->actingAs($manager)
        ->get(route('orders.index', [
            'from_date' => now()->subDays(2)->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]))
        ->assertOk()
        ->assertSeeText('Recent Customer')
        ->assertDontSeeText('Older Customer')
        ->assertSeeText('Displayed Revenue')
        ->assertSeeText('Paid Revenue')
        ->assertSeeText('₱310.00');
});

it('uses separate validation bags for manager service and order forms', function () {
    ['organization' => $organization, 'shop' => $shop, 'service' => $service, 'order' => $order] = createOwnerDashboardContext();
    $manager = createUser();

    Membership::create([
        'user_id' => $manager->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    $this->actingAs($manager)
        ->from(route('services.index'))
        ->post(route('services.store'), ['name' => ''])
        ->assertRedirect(route('services.index'))
        ->assertSessionHasErrorsIn('serviceCreate', ['name']);

    $this->actingAs($manager)
        ->from(route('services.index'))
        ->post(route('shop-services.store'), [
            'shop_id' => $shop->id,
            'service_id' => $service->id,
            'price' => -1,
        ])
        ->assertRedirect(route('services.index'))
        ->assertSessionHasErrorsIn('shopServiceCreate', ['service_id', 'price']);

    $this->actingAs($manager)
        ->from(route('orders.index'))
        ->patch(route('orders.update', $order), [
            'order_id' => $order->id,
            'status' => 'not-a-status',
            'payment_status' => 'paid',
        ])
        ->assertRedirect(route('orders.index'))
        ->assertSessionHasErrorsIn('orderUpdate-'.$order->id, ['status']);

    $this->actingAs($manager)
        ->from(route('orders.index'))
        ->post(route('orders.store'), [
            'customer_name' => '',
            'customer_email' => 'invalid-email',
            'customer_contact_number' => '',
            'shop_id' => $shop->id,
            'shop_service_id' => 999999,
            'service_mode' => 'pickup_only',
            'pickup_address' => '',
            'delivery_address' => '',
            'weight' => -1,
            'pickup_datetime' => '',
            'delivery_datetime' => '',
            'payment_method' => 'credit-card',
            'payment_status' => 'partially-paid',
        ])
        ->assertRedirect(route('orders.index'))
        ->assertSessionHasErrorsIn('orderCreate', ['customer_name', 'customer_email', 'shop_service_id', 'pickup_address', 'weight', 'pickup_datetime', 'payment_method', 'payment_status']);
});

it('lets an owner add and remove organization members from the dashboard', function () {
    ['owner' => $owner, 'organization' => $organization] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $response = $this->actingAs($owner)
        ->post(route('memberships.store'), [
            'name' => 'Team Member',
            'email' => 'team.member@example.com',
            'contact_number' => '09179998888',
            'role' => 'staff',
            'shop_id' => $organization->shops()->value('id'),
        ]);

    $response->assertRedirect(route('memberships.index'))
        ->assertSessionHas('success', 'Member added!')
        ->assertSessionHas('memberInvite', fn (array $invite) => $invite['email'] === 'team.member@example.com'
            && $invite['name'] === 'Team Member'
            && filled($invite['reset_url'])
            && str_contains($invite['reset_url'], '/reset-password/')
            && str_contains($invite['reset_url'], 'email=team.member%40example.com'));

    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => 'team.member@example.com',
    ]);

    $member = User::query()->where('email', 'team.member@example.com')->firstOrFail();
    $membership = Membership::query()->where('user_id', $member->id)->where('organization_id', $organization->id)->firstOrFail();

    expect($membership->role)->toBe('staff');
    expect($membership->shop_id)->toBe($organization->shops()->value('id'));

    $this->actingAs($owner)
        ->delete(route('memberships.destroy', $membership))
        ->assertRedirect(route('memberships.index'));

    $this->assertDatabaseMissing('memberships', [
        'id' => $membership->id,
    ]);
});

it('does not expose temporary credentials when adding an existing user as a member', function () {
    ['owner' => $owner, 'organization' => $organization] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $existingUser = createUser();

    $response = $this->actingAs($owner)
        ->post(route('memberships.store'), [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'contact_number' => $existingUser->contact_number,
            'role' => 'manager',
            'shop_id' => $organization->shops()->value('id'),
        ]);

    $response->assertRedirect(route('memberships.index'))
        ->assertSessionHas('success', 'Member added!')
        ->assertSessionMissing('memberInvite');

    $this->assertDatabaseHas('memberships', [
        'user_id' => $existingUser->id,
        'organization_id' => $organization->id,
        'role' => 'manager',
    ]);
});

it('lets an owner update a member role from the dashboard', function () {
    ['owner' => $owner, 'organization' => $organization] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $member = createUser();

    $membership = Membership::create([
        'user_id' => $member->id,
        'organization_id' => $organization->id,
        'shop_id' => $organization->shops()->value('id'),
        'role' => 'staff',
    ]);

    $this->actingAs($owner)
        ->patch(route('memberships.update', $membership), [
            'membership_id' => $membership->id,
            'role' => 'manager',
            'shop_id' => $organization->shops()->value('id'),
        ])
        ->assertRedirect(route('memberships.index'));

    $this->assertDatabaseHas('memberships', [
        'id' => $membership->id,
        'role' => 'manager',
    ]);
});

it('filters members on the memberships page by search term and role', function () {
    ['owner' => $owner, 'organization' => $organization] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $managerUser = User::create([
        'name' => 'Alice Manager',
        'email' => 'alice.manager@example.com',
        'password' => bcrypt('password'),
        'contact_number' => '09175550001',
    ]);

    $staffUser = User::create([
        'name' => 'Brian Staff',
        'email' => 'brian.staff@example.com',
        'password' => bcrypt('password'),
        'contact_number' => '09175550002',
    ]);

    Membership::create([
        'user_id' => $managerUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $organization->shops()->value('id'),
        'role' => 'manager',
    ]);

    Membership::create([
        'user_id' => $staffUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $organization->shops()->value('id'),
        'role' => 'staff',
    ]);

    $this->actingAs($owner)
        ->get(route('memberships.index', ['member_search' => 'Alice']))
        ->assertOk()
        ->assertSeeText('Alice Manager')
        ->assertDontSeeText('Brian Staff');

    $this->actingAs($owner)
        ->get(route('memberships.index', ['member_role' => 'staff']))
        ->assertOk()
        ->assertSeeText('Brian Staff')
        ->assertDontSeeText('Alice Manager');
});

it('uses a separate validation bag for the membership form', function () {
    ['owner' => $owner] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $this->actingAs($owner)
        ->from(route('memberships.index'))
        ->post(route('memberships.store'), [
            'name' => '',
            'email' => 'invalid-email',
            'contact_number' => '',
            'role' => 'owner',
            'shop_id' => '',
        ])
        ->assertRedirect(route('memberships.index'))
        ->assertSessionHasErrorsIn('membershipCreate', ['name', 'email', 'role', 'shop_id']);
});

it('uses a separate validation bag for membership role updates', function () {
    ['owner' => $owner, 'organization' => $organization] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $member = createUser();

    $membership = Membership::create([
        'user_id' => $member->id,
        'organization_id' => $organization->id,
        'shop_id' => $organization->shops()->value('id'),
        'role' => 'staff',
    ]);

    $this->actingAs($owner)
        ->from(route('memberships.index'))
        ->patch(route('memberships.update', $membership), [
            'membership_id' => $membership->id,
            'role' => 'owner',
            'shop_id' => '',
        ])
        ->assertRedirect(route('memberships.index'))
        ->assertSessionHasErrorsIn('membershipUpdate-'.$membership->id, ['role', 'shop_id']);
});

it('lets managers handle services and orders but not owner-only shop or membership management', function () {
    ['organization' => $organization, 'shop' => $shop, 'service' => $service, 'order' => $order] = createOwnerDashboardContext();

    $unassignedService = Service::create([
        'organization_id' => $organization->id,
        'name' => 'Press Only',
    ]);

    $managerUser = createUser();

    Membership::create([
        'user_id' => $managerUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    $memberToRemove = createUser();

    $membershipToRemove = Membership::create([
        'user_id' => $memberToRemove->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    $this->actingAs($managerUser)
        ->post(route('shops.store'), [
            'organization_id' => $organization->id,
            'shop_name' => 'Managed Shop',
            'address' => 'No Access Street',
            'contact_number' => '09170000000',
            'description' => 'Created by manager',
        ])
        ->assertForbidden();

    $this->actingAs($managerUser)
        ->post(route('services.store'), [
            'name' => 'Managed Service',
        ])
        ->assertRedirect(route('services.index'));

    $this->actingAs($managerUser)
        ->post(route('shop-services.store'), [
            'shop_id' => $shop->id,
            'service_id' => $unassignedService->id,
            'price' => 999,
        ])
        ->assertRedirect(route('services.index'));

    $this->actingAs($managerUser)
        ->patch(route('orders.update', $order), [
            'order_id' => $order->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ])
        ->assertRedirect(route('orders.index'));

    $this->actingAs($managerUser)
        ->post(route('memberships.store'), [
            'name' => 'Managed Member',
            'email' => 'managed.member@example.com',
            'contact_number' => '09178888888',
            'role' => 'staff',
        ])
        ->assertForbidden();

    $this->actingAs($managerUser)
        ->patch(route('memberships.update', $membershipToRemove), [
            'membership_id' => $membershipToRemove->id,
            'role' => 'staff',
        ])
        ->assertForbidden();

    $this->actingAs($managerUser)
        ->delete(route('memberships.destroy', $membershipToRemove))
        ->assertForbidden();

    $this->assertDatabaseMissing('shops', [
        'shop_name' => 'Managed Shop',
    ]);

    $this->assertDatabaseHas('services', [
        'name' => 'Managed Service',
        'organization_id' => $organization->id,
    ]);

    $this->assertDatabaseHas('shop_services', [
        'shop_id' => $shop->id,
        'service_id' => $unassignedService->id,
        'price' => 999,
    ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'completed',
        'payment_status' => 'paid',
    ]);

    $this->assertDatabaseMissing('users', [
        'email' => 'managed.member@example.com',
    ]);

    $this->assertDatabaseHas('memberships', [
        'id' => $membershipToRemove->id,
    ]);
});

it('lets staff manage orders but forbids membership and manager-only shop or service management', function () {
    ['organization' => $organization, 'shop' => $shop, 'service' => $service, 'order' => $order, 'shopService' => $shopService] = createOwnerDashboardContext();

    $staffUser = createUser();

    Membership::create([
        'user_id' => $staffUser->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'staff',
    ]);

    $memberToUpdate = createUser();

    $membershipToUpdate = Membership::create([
        'user_id' => $memberToUpdate->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    $this->actingAs($staffUser)
        ->get(route('orders.index'))
        ->assertOk();

    $this->actingAs($staffUser)
        ->post(route('orders.store'), [
            'customer_name' => 'Staff Created Customer',
            'customer_email' => 'staff.created.customer@example.com',
            'customer_contact_number' => '09176660000',
            'shop_id' => $shop->id,
            'shop_service_id' => $shopService->id,
            'service_mode' => 'pickup_only',
            'pickup_address' => '123 Staff Pickup',
            'delivery_address' => '',
            'weight' => 1.25,
            'pickup_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_datetime' => '',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ])
        ->assertRedirect(route('orders.index'));

    $this->actingAs($staffUser)
        ->patch(route('orders.update', $order), [
            'order_id' => $order->id,
            'status' => 'completed',
            'payment_status' => 'paid',
        ])
        ->assertRedirect(route('orders.index'));

    $this->actingAs($staffUser)
        ->get(route('memberships.index'))
        ->assertForbidden();

    $this->actingAs($staffUser)
        ->post(route('memberships.store'), [
            'name' => 'Blocked Member',
            'email' => 'blocked.member@example.com',
            'contact_number' => '09178888888',
            'role' => 'staff',
            'shop_id' => $shop->id,
        ])
        ->assertForbidden();

    $this->actingAs($staffUser)
        ->patch(route('memberships.update', $membershipToUpdate), [
            'membership_id' => $membershipToUpdate->id,
            'role' => 'staff',
            'shop_id' => $shop->id,
        ])
        ->assertForbidden();

    $this->actingAs($staffUser)
        ->post(route('services.store'), [
            'name' => 'Unauthorized Service',
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('warning', 'Only managers can manage services. Owners can manage shops and member roles from the dashboard.');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'completed',
        'payment_status' => 'paid',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'staff.created.customer@example.com',
    ]);
});

function createOwnerDashboardContext(bool $includeOrder = true, bool $includeService = true): array
{
    $owner = createUser();

    $organization = Organization::create([
        'name' => 'BubbleLink Laundry',
        'owner_user_id' => $owner->id,
    ]);

    Membership::create([
        'user_id' => $owner->id,
        'organization_id' => $organization->id,
        'role' => 'owner',
    ]);

    $shop = Shop::create([
        'organization_id' => $organization->id,
        'shop_name' => 'BubbleLink Central',
        'address' => '123 Main Street',
        'contact_number' => '09171234567',
        'description' => 'Flagship branch',
    ]);

    $service = null;
    $shopService = null;
    $order = null;

    if ($includeService) {
        $service = Service::create([
            'organization_id' => $organization->id,
            'name' => 'Wash and Fold',
        ]);

        $shopService = ShopService::create([
            'shop_id' => $shop->id,
            'service_id' => $service->id,
            'price' => 150.50,
        ]);
    }

    if ($includeOrder) {
        $customer = createUser();

        $order = Order::create([
            'customer_id' => $customer->id,
            'shop_id' => $shop->id,
            'shop_service_id' => $shopService->id,
            'service_mode' => 'pickup_only',
            'pickup_address' => '456 Pickup Street',
            'delivery_address' => null,
            'weight' => 4.25,
            'pickup_datetime' => now()->addDay(),
            'delivery_datetime' => now()->addDays(2),
            'total_price' => 150.50,
            'status' => 'pending',
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ]);
    }

    return [
        'owner' => $owner,
        'organization' => $organization,
        'shop' => $shop,
        'service' => $service,
        'shopService' => $shopService,
        'order' => $order,
    ];
}

function createUser(): User
{
    return User::create([
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => bcrypt('password'),
        'contact_number' => fake()->numerify('09#########'),
    ]);
}
