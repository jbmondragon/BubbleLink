<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('Customer registration');
});

test('admin registration screen can be rendered', function () {
    $response = $this->get('/admin/register');

    $response->assertStatus(200);
    $response->assertSee('Shop Owner registration');
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
    $response = $this->post('/admin/register', [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'contact_number' => '09995554444',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    expect(User::query()->where('email', 'admin@example.com')->value('contact_number'))->toBe('09995554444');
    expect(User::query()->where('email', 'admin@example.com')->value('owner_registration_status'))->toBe('pending');
    $response->assertRedirect(route('admin.login', absolute: false));
    $response->assertSessionHas('success', 'Shop owner registration submitted. Wait for platform admin approval before logging in.');
});
