<?php

use App\Models\Shop;
use App\Models\User;

test('login screens can be rendered', function () {
    $this->get('/login')->assertOk()->assertSee('Customer login');
    $this->get('/shop-owner/login')->assertOk()->assertSee('Shop Owner login');
    $this->get('/platform-admin/login')->assertOk()->assertSee('Platform Admin login');
});

test('demo credentials can prefill the customer login screen', function () {
    $this->get('/customer/login?demo_email=bob@example.com&demo_password=password')
        ->assertOk()
        ->assertSee('Demo credentials loaded for')
        ->assertSee('value="bob@example.com"', false)
        ->assertSee('value="password"', false);
});

test('customers can authenticate using the customer login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('customer.shops.index', absolute: false));
});

test('approved shop owners can not authenticate using the customer login screen', function () {
    $user = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/login');
    $response->assertSessionHasErrors([
        'email' => 'Please check again your login credentials.',
    ]);
});

test('platform admins can not authenticate using the customer login screen', function () {
    $user = User::factory()->create([
        'is_platform_admin' => true,
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/login');
    $response->assertSessionHasErrors([
        'email' => 'Please check again your login credentials.',
    ]);
});

test('approved shop owners without shops are redirected to the dashboard after admin login', function () {
    $user = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $response = $this->post('/shop-owner/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
    $response->assertSessionHas('success', 'Shop owner account approved. Finish your first shop profile to get started.');
});

test('approved shop owners with shops are redirected to the dashboard after admin login', function () {
    $user = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    Shop::create([
        'owner_user_id' => $user->id,
        'shop_name' => 'Owner Shop',
        'address' => '123 Owner Street',
        'contact_number' => '09170000000',
        'description' => 'Owner-managed branch',
    ]);

    $response = $this->post('/shop-owner/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('pending shop owners can not authenticate using the shop owner login screen', function () {
    $user = User::factory()->create([
        'owner_registration_status' => 'pending',
    ]);

    $response = $this->from('/shop-owner/login')->post('/shop-owner/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/shop-owner/login');
    $response->assertSessionHasErrors([
        'email' => 'Your shop owner registration is still pending approval.',
    ]);
});

test('platform admins are redirected to owner approvals after login', function () {
    $user = User::factory()->create([
        'is_platform_admin' => true,
    ]);

    $response = $this->post('/platform-admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('platform-admin.owner-registrations.index', absolute: false));
});

test('approved owners without shops can still open the fallback first-shop form', function () {
    $user = User::factory()->create([
        'owner_registration_status' => 'approved',
    ]);

    $this->actingAs($user)
        ->get(route('shops.create'))
        ->assertOk()
        ->assertSee('Create Shop');
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
