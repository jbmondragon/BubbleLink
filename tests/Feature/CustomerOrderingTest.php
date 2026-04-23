<?php

use App\Models\Membership;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;

function createCustomerOrderingContext(): array
{
    $owner = User::factory()->create();

    $organization = Organization::create([
        'name' => 'QuickClean Laundry',
        'owner_user_id' => $owner->id,
    ]);

    Membership::create([
        'user_id' => $owner->id,
        'organization_id' => $organization->id,
        'role' => 'owner',
    ]);

    $shop = Shop::create([
        'organization_id' => $organization->id,
        'shop_name' => 'QuickClean Manila',
        'address' => '123 Sampaloc Street, Manila',
        'contact_number' => '09123456789',
        'description' => 'Express wash and fold services.',
    ]);

    $service = Service::create([
        'organization_id' => $organization->id,
        'name' => 'Wash, Dry, Fold',
    ]);

    $shopService = ShopService::create([
        'shop_id' => $shop->id,
        'service_id' => $service->id,
        'price' => 180.00,
    ]);

    return compact('owner', 'organization', 'shop', 'service', 'shopService');
}

test('guests can browse shops and view shop details', function () {
    $context = createCustomerOrderingContext();

    $this->get(route('customer.shops.index'))
        ->assertOk()
        ->assertSee('QuickClean Manila')
        ->assertSee('QuickClean Laundry')
        ->assertSee('Customer Login')
        ->assertSee('Admin Login')
        ->assertSee('Seeded Demo Accounts')
        ->assertSee('bob@example.com')
        ->assertSee('john@example.com')
        ->assertSee('Copy credentials')
        ->assertSee('One-click customer login')
        ->assertSee('One-click admin login');

    $this->get(route('customer.shops.show', $context['shop']))
        ->assertOk()
        ->assertSee('Wash, Dry, Fold')
        ->assertSee('Log in to order');
});

test('authenticated customers can browse shops without memberships', function () {
    createCustomerOrderingContext();
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('customer.shops.index'))
        ->assertOk()
        ->assertSee('Find a shop near you')
        ->assertSee('View my orders');
});

test('authenticated customers can place orders and view them', function () {
    $context = createCustomerOrderingContext();
    $customer = User::factory()->create([
        'name' => 'Mia Customer',
        'contact_number' => '09998887777',
    ]);

    $this->actingAs($customer)
        ->post(route('customer.orders.store', $context['shop']), [
            'shop_service_id' => $context['shopService']->id,
            'weight' => 4.5,
            'service_mode' => 'both',
            'pickup_address' => '45 Scout Area, Quezon City',
            'delivery_address' => '45 Scout Area, Quezon City',
            'pickup_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
            'delivery_datetime' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ])
        ->assertRedirect();

    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'customer_id' => $customer->id,
        'shop_id' => $context['shop']->id,
        'shop_service_id' => $context['shopService']->id,
        'service_mode' => 'both',
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    $this->actingAs($customer)
        ->get(route('customer.orders.index'))
        ->assertOk()
        ->assertSee('My Orders')
        ->assertSee('QuickClean Manila');

    $this->actingAs($customer)
        ->get(route('customer.orders.show', $order))
        ->assertOk()
        ->assertSee('Order #'.$order->id)
        ->assertSee('Wash, Dry, Fold');
});

test('customers can only view their own orders', function () {
    $context = createCustomerOrderingContext();
    $customer = User::factory()->create();
    $otherCustomer = User::factory()->create();

    $order = Order::create([
        'customer_id' => $customer->id,
        'shop_id' => $context['shop']->id,
        'shop_service_id' => $context['shopService']->id,
        'service_mode' => 'pickup_only',
        'pickup_address' => '123 Main Street',
        'delivery_address' => null,
        'weight' => 3.2,
        'pickup_datetime' => now()->addDay(),
        'delivery_datetime' => null,
        'total_price' => $context['shopService']->price,
        'status' => 'pending',
        'payment_method' => null,
        'payment_status' => 'unpaid',
    ]);

    $this->actingAs($otherCustomer)
        ->get(route('customer.orders.show', $order))
        ->assertForbidden();
});
