<?php

use App\Models\Order;
use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;

it('loads the owner dashboard with shop, revenue, and order overview data', function () {
    ['owner' => $owner, 'shop' => $shop, 'order' => $order] = createOwnerDashboardContext();

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('shopCount', 1)
        ->assertViewHas('totalOrders', 1)
        ->assertViewHas('totalRevenue', fn ($totalRevenue) => (float) $totalRevenue === (float) $order->total_price)
        ->assertSeeText('My Dashboard')
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('Shop Workspace')
        ->assertSeeText('Services')
        ->assertSeeText('Orders')
        ->assertDontSee(route('shops.create'), false)
        ->assertSee(route('services.index'), false)
        ->assertSee(route('orders.index'), false);
});

it('shows the fixed service options and lets an owner assign one directly', function () {
    ['owner' => $owner, 'shop' => $shop] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertOk()
        ->assertSeeText('Wash, Dry, Fold')
        ->assertSeeText('Dry Cleaning')
        ->assertSeeText('Ironing Only')
        ->assertSeeText('Wash, Dry, Fold, Iron')
        ->assertSeeText($shop->shop_name);

    $service = Service::query()
        ->where('shop_id', $shop->id)
        ->where('name', 'Dry Cleaning')
        ->first();

    expect($service)->not->toBeNull();

    $this->actingAs($owner)
        ->post(route('shop-services.store'), [
            'shop_id' => $shop->id,
            'service_id' => $service->id,
            'price' => 175.00,
        ])
        ->assertRedirect(route('services.index'));

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertOk()
        ->assertSeeText('Dry Cleaning')
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('₱175.00');
});

it('lets an owner access and manage internal orders directly', function () {
    ['owner' => $owner, 'shop' => $shop, 'shopService' => $shopService] = createOwnerDashboardContext(includeOrder: false);

    $this->actingAs($owner)
        ->post(route('orders.store'), [
            'customer_name' => 'Walk-in Customer',
            'customer_email' => 'walkin.customer@example.com',
            'customer_contact_number' => '09170001111',
            'shop_id' => $shop->id,
            'shop_service_id' => $shopService->id,
            'service_mode' => 'delivery_only',
            'delivery_address' => '789 Delivery Avenue',
            'weight' => 5.50,
            'delivery_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ])
        ->assertRedirect(route('orders.index'));

    $this->actingAs($owner)
        ->get(route('orders.index'))
        ->assertOk()
        ->assertSeeText($shop->shop_name)
        ->assertSeeText('Walk-in Customer');
});

it('redirects approved owners without shops to create a shop before services or orders', function () {
    $owner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertRedirect(route('shops.create'))
        ->assertSessionHas('warning', 'Create your first shop before managing services.');

    $this->actingAs($owner)
        ->get(route('orders.index'))
        ->assertRedirect(route('shops.create'))
        ->assertSessionHas('warning', 'Create your first shop before managing orders.');
});

it('blocks repeat shop creation for owners who already have a shop', function () {
    ['owner' => $owner] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $this->actingAs($owner)
        ->get(route('shops.create'))
        ->assertForbidden();
});

it('shows owner shops on the public catalog page', function () {
    ['owner' => $owner, 'shop' => $ownedShop] = createOwnerDashboardContext(includeOrder: false, includeService: false);

    $otherOwner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $otherShop = Shop::create([
        'owner_user_id' => $otherOwner->id,
        'shop_name' => 'Outside Branch',
        'address' => '789 Other Street',
        'contact_number' => '09179999999',
        'description' => 'Another laundry shop',
    ]);

    $this->actingAs($owner)
        ->get(route('customer.shops.index'))
        ->assertOk()
        ->assertSeeText($ownedShop->shop_name)
        ->assertSeeText($otherShop->shop_name);
});

function createOwnerDashboardContext(bool $includeOrder = true, bool $includeService = true): array
{
    $owner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $shop = Shop::create([
        'owner_user_id' => $owner->id,
        'shop_name' => 'BubbleLink Laundry Hub',
        'address' => '123 Laundry Street',
        'contact_number' => '09171234567',
        'description' => 'Main owner-managed branch.',
    ]);

    $service = null;
    $shopService = null;
    $order = null;

    if ($includeService) {
        $service = Service::create([
            'shop_id' => $shop->id,
            'name' => 'Wash, Dry, Fold',
        ]);

        $shopService = ShopService::create([
            'shop_id' => $shop->id,
            'service_id' => $service->id,
            'price' => 150.50,
        ]);
    }

    if ($includeOrder && $shopService) {
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
    }

    return compact('owner', 'shop', 'service', 'shopService', 'order');
}
