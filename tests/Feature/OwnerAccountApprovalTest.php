<?php

use App\Models\OwnerRegistrationReview;
use App\Models\User;
use App\Notifications\ShopOwnerRegistrationApprovedNotification;
use App\Notifications\ShopOwnerRegistrationRejectedNotification;
use Illuminate\Support\Facades\Notification;

test('platform admin can approve a pending shop owner registration', function () {
    Notification::fake();

    $platformAdmin = User::factory()->create([
        'is_platform_admin' => true,
    ]);
    $pendingOwner = User::factory()->create([
        'owner_registration_status' => 'pending',
    ]);

    $response = $this->actingAs($platformAdmin)->patch(route('platform-admin.owner-registrations.approve', $pendingOwner));

    $response->assertRedirect(route('platform-admin.owner-registrations.index'));
    $response->assertSessionHas('success', 'Shop owner registration approved.');

    $this->assertDatabaseHas('users', [
        'id' => $pendingOwner->id,
        'owner_registration_status' => 'approved',
        'approved_by_user_id' => $platformAdmin->id,
    ]);

    $this->assertDatabaseHas('owner_registration_reviews', [
        'shop_owner_user_id' => $pendingOwner->id,
        'platform_admin_user_id' => $platformAdmin->id,
        'action' => 'approved',
        'previous_status' => 'pending',
        'new_status' => 'approved',
    ]);

    Notification::assertSentTo($pendingOwner, ShopOwnerRegistrationApprovedNotification::class);
});

test('platform admin can reject a pending shop owner registration', function () {
    Notification::fake();

    $platformAdmin = User::factory()->create([
        'is_platform_admin' => true,
    ]);
    $pendingOwner = User::factory()->create([
        'owner_registration_status' => 'pending',
    ]);

    $response = $this->actingAs($platformAdmin)->patch(route('platform-admin.owner-registrations.reject', $pendingOwner));

    $response->assertRedirect(route('platform-admin.owner-registrations.index'));
    $response->assertSessionHas('success', 'Shop owner registration rejected.');

    $this->assertDatabaseHas('users', [
        'id' => $pendingOwner->id,
        'owner_registration_status' => 'rejected',
        'approved_by_user_id' => $platformAdmin->id,
    ]);

    $this->assertDatabaseHas('owner_registration_reviews', [
        'shop_owner_user_id' => $pendingOwner->id,
        'platform_admin_user_id' => $platformAdmin->id,
        'action' => 'rejected',
        'previous_status' => 'pending',
        'new_status' => 'rejected',
    ]);

    Notification::assertSentTo($pendingOwner, ShopOwnerRegistrationRejectedNotification::class);
});

test('non platform admins can not access owner approval routes', function () {
    $user = User::factory()->create();
    $pendingOwner = User::factory()->create([
        'owner_registration_status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('platform-admin.owner-registrations.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->patch(route('platform-admin.owner-registrations.approve', $pendingOwner))
        ->assertForbidden();
});

test('platform admin owner approval page shows audit log history', function () {
    $platformAdmin = User::factory()->create([
        'is_platform_admin' => true,
    ]);
    $pendingOwner = User::factory()->create([
        'name' => 'Pending Owner',
        'owner_registration_status' => 'approved',
        'approved_by_user_id' => $platformAdmin->id,
        'owner_registration_reviewed_at' => now(),
    ]);

    OwnerRegistrationReview::create([
        'shop_owner_user_id' => $pendingOwner->id,
        'platform_admin_user_id' => $platformAdmin->id,
        'action' => 'approved',
        'previous_status' => 'pending',
        'new_status' => 'approved',
    ]);

    $this->actingAs($platformAdmin)
        ->get(route('platform-admin.owner-registrations.index'))
        ->assertOk()
        ->assertSee('Audit log')
        ->assertSee('Pending Owner')
        ->assertSee('approved');
});
