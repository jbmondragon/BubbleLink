<?php

use App\Models\Order;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;

function createCustomerOrderModeContext(): array
{
    $owner = User::factory()->create();

    $organization = Organization::create([
        'name' => 'Seafoam Laundry',
        'owner_user_id' => $owner->id,
    ]);

    $shop = Shop::create([
        'organization_id' => $organization->id,
        'shop_name' => 'Seafoam Makati',
        'address' => '42 Dela Rosa Street, Makati',
        'contact_number' => '09171234567',
        'description' => 'Pickup, delivery, and walk-in laundry orders.',
    ]);

    $service = Service::create([
        'organization_id' => $organization->id,
        'name' => 'Wash and Fold',
    ]);

    $shopService = ShopService::create([
        'shop_id' => $shop->id,
        'service_id' => $service->id,
        'price' => 220.00,
    ]);

    return compact('shop', 'shopService');
}

function validCustomerOrderPayload(array $context, array $overrides = []): array
{
    return array_merge([
        'shop_service_id' => $context['shopService']->id,
        'service_mode' => 'both',
        'pickup_address' => '12 Olive Street, Quezon City',
        'delivery_address' => '34 Pine Street, Pasig',
        'pickup_datetime' => '2030-05-01T09:00',
        'delivery_datetime' => '2030-05-02T15:30',
    ], $overrides);
}

test('walk in orders do not require transport details and discard unexpected transport fields', function () {
    $context = createCustomerOrderModeContext();
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->post(route('customer.orders.store', $context['shop']), validCustomerOrderPayload($context, [
            'service_mode' => 'walk_in',
            'pickup_address' => 'Should be ignored',
            'delivery_address' => 'Should be ignored',
            'pickup_datetime' => '2030-05-05T09:45',
            'delivery_datetime' => '2030-05-06T13:15',
        ]))
        ->assertRedirect();

    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull();

    expect($order->service_mode)->toBe('walk_in')
        ->and($order->pickup_address)->toBeNull()
        ->and($order->delivery_address)->toBeNull()
        ->and($order->weight)->toBeNull()
        ->and($order->pickup_datetime)->toBeNull()
        ->and($order->delivery_datetime)->toBeNull();
});

test('service mode stores only the matching transport details', function (string $serviceMode, array $overrides, array $expected) {
    $context = createCustomerOrderModeContext();
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->post(route('customer.orders.store', $context['shop']), validCustomerOrderPayload($context, array_merge([
            'service_mode' => $serviceMode,
        ], $overrides)))
        ->assertRedirect();

    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull();

    expect($order->service_mode)->toBe($serviceMode)
        ->and($order->pickup_address)->toBe($expected['pickup_address'])
        ->and(optional($order->pickup_datetime)?->format('Y-m-d H:i:s'))->toBe($expected['pickup_datetime'])
        ->and($order->delivery_address)->toBe($expected['delivery_address'])
        ->and(optional($order->delivery_datetime)?->format('Y-m-d H:i:s'))->toBe($expected['delivery_datetime']);
})->with([
    'pickup only' => [
        'pickup_only',
        [
            'delivery_address' => 'Should be ignored',
            'delivery_datetime' => '2030-05-08T16:30',
        ],
        [
            'pickup_address' => '12 Olive Street, Quezon City',
            'pickup_datetime' => '2030-05-01 09:00:00',
            'delivery_address' => null,
            'delivery_datetime' => null,
        ],
    ],
    'delivery only' => [
        'delivery_only',
        [
            'pickup_address' => 'Should be ignored',
            'pickup_datetime' => '2030-05-07T08:00',
        ],
        [
            'pickup_address' => null,
            'pickup_datetime' => null,
            'delivery_address' => '34 Pine Street, Pasig',
            'delivery_datetime' => '2030-05-02 15:30:00',
        ],
    ],
    'both' => [
        'both',
        [],
        [
            'pickup_address' => '12 Olive Street, Quezon City',
            'pickup_datetime' => '2030-05-01 09:00:00',
            'delivery_address' => '34 Pine Street, Pasig',
            'delivery_datetime' => '2030-05-02 15:30:00',
        ],
    ],
]);

test('service mode validates only the required transport fields', function (string $serviceMode, array $overrides, array $expectedErrors) {
    $context = createCustomerOrderModeContext();
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->from(route('customer.orders.create', $context['shop']))
        ->post(route('customer.orders.store', $context['shop']), validCustomerOrderPayload($context, array_merge([
            'service_mode' => $serviceMode,
        ], $overrides)))
        ->assertRedirect(route('customer.orders.create', $context['shop']))
        ->assertSessionHasErrorsIn('customerOrderCreate', $expectedErrors);

    expect(Order::query()->count())->toBe(0);
})->with([
    'pickup only requires pickup details' => [
        'pickup_only',
        [
            'pickup_address' => '',
            'pickup_datetime' => '',
        ],
        ['pickup_address', 'pickup_datetime'],
    ],
    'delivery only requires delivery details' => [
        'delivery_only',
        [
            'delivery_address' => '',
            'delivery_datetime' => '',
        ],
        ['delivery_address', 'delivery_datetime'],
    ],
    'both requires pickup and delivery details' => [
        'both',
        [
            'pickup_address' => '',
            'pickup_datetime' => '',
            'delivery_address' => '',
            'delivery_datetime' => '',
        ],
        ['pickup_address', 'pickup_datetime', 'delivery_address', 'delivery_datetime'],
    ],
]);
