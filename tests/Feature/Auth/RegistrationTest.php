<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('Customer registration');
});

test('admin registration screen can be rendered', function () {
    $response = $this->get('/shop-owner/register');

    $response->assertStatus(200);
    $response->assertSee('Shop Owner registration');
    $response->assertSee('First Shop Details');
    $response->assertSee('Shop Name');
    $response->assertSee('Shop Address');
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'contact_number' => '09123456789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    expect(User::query()->where('email', 'test@example.com')->value('contact_number'))->toBe('09123456789');
    $response->assertRedirect(route('customer.shops.index', absolute: false));
});

test('new shop owners can register and await approval', function () {
    $response = $this->post('/shop-owner/register', [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'contact_number' => '09995554444',
        'shop_name' => 'Admin Laundry Hub',
        'shop_address' => '101 Registration Street',
        'shop_contact_number' => '09995554445',
        'shop_description' => 'First shop submitted during registration.',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    expect(User::query()->where('email', 'admin@example.com')->value('contact_number'))->toBe('09995554444');
    expect(User::query()->where('email', 'admin@example.com')->value('owner_registration_status'))->toBe('pending');
    expect(User::query()->where('email', 'admin@example.com')->value('pending_shop_name'))->toBe('Admin Laundry Hub');
    expect(User::query()->where('email', 'admin@example.com')->value('pending_shop_address'))->toBe('101 Registration Street');
    $response->assertRedirect(route('admin.login', absolute: false));
    $response->assertSessionHas('success', 'Shop owner registration submitted. Wait for platform admin approval before logging in.');
});

test('shop owner registration requires first shop details', function () {
    $response = $this->from('/shop-owner/register')->post('/shop-owner/register', [
        'name' => 'Incomplete Owner',
        'email' => 'incomplete-owner@example.com',
        'contact_number' => '09123450000',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/shop-owner/register');
    $response->assertSessionHasErrors(['shop_name', 'shop_address']);
});
