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
    $response->assertSee('Admin registration');
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

test('new admins can register and are redirected to admin onboarding', function () {
    $response = $this->post('/admin/register', [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'contact_number' => '09995554444',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    expect(User::query()->where('email', 'admin@example.com')->value('contact_number'))->toBe('09995554444');
    $response->assertRedirect(route('admin.start', absolute: false));
    $response->assertSessionHas('success', 'Admin account created. Set up your organization to start managing your business.');
});
