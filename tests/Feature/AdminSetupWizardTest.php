<?php

use App\Models\Service;
use App\Models\Shop;
use App\Models\User;

test('approved owners can manage services immediately after approval-created shop bootstrap', function () {
    $owner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $shop = Shop::create([
        'owner_user_id' => $owner->id,
        'shop_name' => 'Bootstrap Branch',
        'address' => '123 Setup Street',
        'contact_number' => '09178889999',
        'description' => 'Created from submitted registration details.',
    ]);

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertOk()
        ->assertSee('Wash, Dry, Fold')
        ->assertSee('Dry Cleaning')
        ->assertSee('Ironing Only')
        ->assertSee('Wash, Dry, Fold, Iron');

    $service = Service::query()->where('shop_id', $shop->id)->where('name', 'Wash, Dry, Fold, Iron')->first();

    expect($service)->not->toBeNull();

    $this->actingAs($owner)
        ->post(route('shop-services.store'), [
            'shop_id' => $shop->id,
            'service_id' => $service->id,
            'price' => 199.50,
        ])
        ->assertRedirect(route('services.index', absolute: false));

    $this->assertDatabaseHas('shop_services', [
        'shop_id' => $shop->id,
        'service_id' => $service->id,
        'price' => '199.5',
    ]);

    $this->actingAs($owner)
        ->get(route('services.index'))
        ->assertOk()
        ->assertSee('Bootstrap Branch')
        ->assertSee('Wash, Dry, Fold, Iron');
});

test('customers can not access the fallback owner shop bootstrap routes', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('shops.create'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->post(route('shops.store'), [
            'shop_name' => 'Blocked Shop',
            'address' => '123 Blocked Street',
        ])
        ->assertForbidden();
});

test('approved owners without a shop can still use the fallback shop creation flow', function () {
    $owner = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $this->actingAs($owner)
        ->post(route('shops.store'), [
            'shop_name' => 'Fallback Branch',
            'address' => '789 Fallback Avenue',
            'contact_number' => '09176667777',
            'description' => 'Used when legacy approved owners have no shop yet.',
        ])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('shops', [
        'owner_user_id' => $owner->id,
        'shop_name' => 'Fallback Branch',
    ]);
});
