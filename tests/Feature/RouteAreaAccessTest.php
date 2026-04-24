<?php

use App\Models\Membership;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;

test('customers are blocked from business, dashboard, and platform admin routes', function () {
    ['shop' => $shop] = createRouteAreaAccessContext();
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('dashboard'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('services.index'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('orders.index'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('memberships.index'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('shops.create'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('shops.show', $shop))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('platform-admin.owner-registrations.index'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('admin.start'))
        ->assertForbidden();
});

test('platform admins can access approval pages but not business or customer routes', function () {
    ['shop' => $shop] = createRouteAreaAccessContext();
    $platformAdmin = User::factory()->create([
        'is_platform_admin' => true,
    ]);

    $this->actingAs($platformAdmin)
        ->get(route('platform-admin.owner-registrations.index'))
        ->assertOk();

    $this->actingAs($platformAdmin)
        ->get(route('dashboard'))
        ->assertRedirect(route('platform-admin.owner-registrations.index'));

    $this->actingAs($platformAdmin)
        ->get(route('services.index'))
        ->assertForbidden();

    $this->actingAs($platformAdmin)
        ->get(route('shops.show', $shop))
        ->assertForbidden();

    $this->actingAs($platformAdmin)
        ->get(route('customer.orders.index'))
        ->assertForbidden();
});

test('approved shop owners without an organization can access setup routes and dashboard', function () {
    $approvedOwner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $this->actingAs($approvedOwner)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('You have not created an organization yet.');

    $this->actingAs($approvedOwner)
        ->get(route('admin.start'))
        ->assertOk();

    $this->actingAs($approvedOwner)
        ->get(route('organizations.create'))
        ->assertOk();

    $this->actingAs($approvedOwner)
        ->get(route('services.index'))
        ->assertRedirect(route('organizations.create'));

    $this->actingAs($approvedOwner)
        ->get(route('orders.index'))
        ->assertRedirect(route('organizations.create'));

    $this->actingAs($approvedOwner)
        ->get(route('memberships.index'))
        ->assertRedirect(route('organizations.create'));

    $this->actingAs($approvedOwner)
        ->get(route('customer.orders.index'))
        ->assertForbidden();
});

test('owners managers and staff are limited to their intended top-level route areas', function () {
    ['owner' => $owner, 'organization' => $organization, 'shop' => $shop] = createRouteAreaAccessContext();
    $manager = User::factory()->create();
    $staff = User::factory()->create();

    Membership::create([
        'user_id' => $manager->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'manager',
    ]);

    Membership::create([
        'user_id' => $staff->id,
        'organization_id' => $organization->id,
        'shop_id' => $shop->id,
        'role' => 'staff',
    ]);

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($owner)
        ->get(route('memberships.index'))
        ->assertOk();

    $this->actingAs($owner)
        ->get(route('shops.create'))
        ->assertOk();

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertRedirect(route('dashboard'));

    $this->actingAs($owner)
        ->get(route('orders.index'))
        ->assertRedirect(route('dashboard'));

    $this->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($manager)
        ->get(route('services.index'))
        ->assertOk();

    $this->actingAs($manager)
        ->get(route('orders.index'))
        ->assertOk();

    $this->actingAs($manager)
        ->get(route('memberships.index'))
        ->assertForbidden();

    $this->actingAs($manager)
        ->get(route('shops.create'))
        ->assertForbidden();

    $this->actingAs($staff)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($staff)
        ->get(route('orders.index'))
        ->assertOk();

    $this->actingAs($staff)
        ->get(route('services.index'))
        ->assertRedirect(route('dashboard'));

    $this->actingAs($staff)
        ->get(route('memberships.index'))
        ->assertForbidden();

    $this->actingAs($staff)
        ->get(route('shops.create'))
        ->assertForbidden();
});

function createRouteAreaAccessContext(): array
{
    $owner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $organization = Organization::create([
        'name' => 'Route Access Laundry',
        'owner_user_id' => $owner->id,
    ]);

    Membership::create([
        'user_id' => $owner->id,
        'organization_id' => $organization->id,
        'role' => 'owner',
    ]);

    $shop = Shop::create([
        'organization_id' => $organization->id,
        'shop_name' => 'Route Access Branch',
        'address' => '123 Access Street',
        'contact_number' => '09171234567',
        'description' => 'Branch used for route access tests.',
    ]);

    $service = Service::create([
        'organization_id' => $organization->id,
        'name' => 'Access Test Service',
    ]);

    $shopService = ShopService::create([
        'shop_id' => $shop->id,
        'service_id' => $service->id,
        'price' => 150.50,
    ]);

    $customer = User::factory()->create();

    $order = Order::create([
        'customer_id' => $customer->id,
        'shop_id' => $shop->id,
        'shop_service_id' => $shopService->id,
        'service_mode' => 'pickup_only',
        'pickup_address' => '456 Pickup Street',
        'delivery_address' => null,
        'weight' => 4.25,
        'pickup_datetime' => now()->addDay(),
        'delivery_datetime' => null,
        'total_price' => 150.50,
        'status' => 'pending',
        'payment_method' => 'cash',
        'payment_status' => 'unpaid',
    ]);

    return compact('owner', 'organization', 'shop', 'service', 'shopService', 'customer', 'order');
}
