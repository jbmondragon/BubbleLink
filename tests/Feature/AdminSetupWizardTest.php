<?php

use App\Models\Membership;
use App\Models\Organization;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;

test('guided organization creation redirects owners into the setup wizard', function () {
    $user = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $response = $this->actingAs($user)->post('/organizations', [
        'name' => 'Wizard Wash Laundry',
        'guided' => 1,
    ]);

    $organization = Organization::query()->where('name', 'Wizard Wash Laundry')->first();

    expect($organization)->not->toBeNull();

    $response->assertRedirect(route('admin.setup', absolute: false));
    $response->assertSessionHas('success', 'Organization created. Continue the guided setup below.');

    $this->assertDatabaseHas('memberships', [
        'user_id' => $user->id,
        'organization_id' => $organization->id,
        'role' => 'owner',
    ]);
});

test('customers can not access organization creation routes', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('organizations.create'))
        ->assertForbidden();

    $this->actingAs($customer)
        ->post(route('organizations.store'), [
            'name' => 'Blocked Customer Organization',
            'guided' => 1,
        ])
        ->assertForbidden();
});

test('owners can complete the guided admin setup wizard', function () {
    $owner = User::factory()->create();
    $organization = Organization::create([
        'name' => 'Setup Wizard Laundry',
        'owner_user_id' => $owner->id,
    ]);

    Membership::create([
        'user_id' => $owner->id,
        'organization_id' => $organization->id,
        'role' => 'owner',
    ]);

    $this->actingAs($owner)
        ->post(route('admin.setup.shop'), [
            'shop_name' => 'Wizard Branch',
            'address' => '123 Setup Street',
            'contact_number' => '09178889999',
            'description' => 'First branch for guided setup.',
        ])
        ->assertRedirect(route('admin.setup', absolute: false));

    $shop = Shop::query()->where('organization_id', $organization->id)->first();

    expect($shop)->not->toBeNull();

    $this->actingAs($owner)
        ->post(route('admin.setup.service'), [
            'shop_id' => $shop->id,
            'name' => 'Premium Fold',
            'price' => 199.50,
        ])
        ->assertRedirect(route('admin.setup', absolute: false));

    $service = Service::query()->where('organization_id', $organization->id)->where('name', 'Premium Fold')->first();

    expect($service)->not->toBeNull();

    $this->actingAs($owner)
        ->post(route('admin.setup.member'), [
            'name' => 'Wizard Manager',
            'email' => 'wizard-manager@example.com',
            'contact_number' => '09176665555',
            'role' => 'manager',
            'shop_id' => $shop->id,
        ])
        ->assertRedirect(route('admin.setup', absolute: false));

    $this->assertDatabaseHas('shop_services', [
        'shop_id' => $shop->id,
        'service_id' => $service->id,
        'price' => '199.50',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'wizard-manager@example.com',
        'name' => 'Wizard Manager',
    ]);

    $this->assertDatabaseHas('memberships', [
        'organization_id' => $organization->id,
        'role' => 'manager',
        'shop_id' => $shop->id,
    ]);

    $this->actingAs($owner)
        ->get(route('admin.setup'))
        ->assertOk()
        ->assertSee('Wizard Branch')
        ->assertSee('Premium Fold')
        ->assertSee('Team size:');
});

test('admins without an owned organization are redirected back to onboarding from the setup wizard', function () {
    $user = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $this->actingAs($user)
        ->get(route('admin.setup'))
        ->assertRedirect(route('admin.start', absolute: false));
});
