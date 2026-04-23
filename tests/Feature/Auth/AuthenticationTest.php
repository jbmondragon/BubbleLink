<?php

use App\Models\Membership;
use App\Models\Organization;
use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Customer login');
});

test('admin login screen can be rendered', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
    $response->assertSee('Admin login');
});

test('demo credentials can prefill the customer login screen', function () {
    $response = $this->get('/customer/login?demo_email=bob@example.com&demo_password=password');

    $response->assertStatus(200);
    $response->assertSee('Demo credentials loaded for');
    $response->assertSee('value="bob@example.com"', false);
    $response->assertSee('value="password"', false);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('customer.shops.index', absolute: false));
});

test('members are redirected to the dashboard after login', function () {
    $user = User::factory()->create();
    $organization = Organization::create([
        'name' => 'QuickClean Laundry',
        'owner_user_id' => $user->id,
    ]);

    Membership::create([
        'user_id' => $user->id,
        'organization_id' => $organization->id,
        'role' => 'owner',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can authenticate using the admin login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.start', absolute: false));
    $response->assertSessionHas('warning', 'No organization is linked to this admin account yet. Start your admin setup to continue.');
});

test('members using the admin login screen are redirected to the dashboard', function () {
    $user = User::factory()->create();
    $organization = Organization::create([
        'name' => 'FreshFold Laundry',
        'owner_user_id' => $user->id,
    ]);

    Membership::create([
        'user_id' => $user->id,
        'organization_id' => $organization->id,
        'role' => 'owner',
    ]);

    $response = $this->post('/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('admin onboarding screen can be rendered for accounts without memberships', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.start'))
        ->assertOk()
        ->assertSee('Set up your organization')
        ->assertSee('Create organization')
        ->assertSee('Onboarding Checklist')
        ->assertSee('Add your first shop');
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
