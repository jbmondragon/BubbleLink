<?php

use App\Models\Order;
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
        ->get(route('shops.create'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('shops.show', $shop))
        ->assertForbidden();

    $this->actingAs($customer)
        ->get(route('platform-admin.owner-registrations.index'))
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

test('approved shop owners without a shop can access dashboard and fallback shop creation routes', function () {
    $approvedOwner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $this->actingAs($approvedOwner)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($approvedOwner)
        ->get(route('shops.create'))
        ->assertOk();

    $this->actingAs($approvedOwner)
        ->get(route('services.index'))
        ->assertRedirect(route('shops.create'));

    $this->actingAs($approvedOwner)
        ->get(route('orders.index'))
        ->assertRedirect(route('shops.create'));

    $this->actingAs($approvedOwner)
        ->get(route('customer.orders.index'))
        ->assertForbidden();
});

test('approved shop owners with a shop can access business routes', function () {
    ['owner' => $owner] = createRouteAreaAccessContext();

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk();

    $this->actingAs($owner)
        ->get(route('shops.create'))
        ->assertForbidden();

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertOk();

    $this->actingAs($owner)
        ->get(route('orders.index'))
        ->assertOk();
});

function createRouteAreaAccessContext(): array
{
    $owner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $shop = Shop::create([
        'owner_user_id' => $owner->id,
        'shop_name' => 'Route Access Branch',
        'address' => '123 Access Street',
        'contact_number' => '09171234567',
        'description' => 'Branch used for route access tests.',
    ]);

    $service = Service::create([
        'shop_id' => $shop->id,
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

    return compact('owner', 'shop', 'service', 'shopService', 'customer', 'order');
}
