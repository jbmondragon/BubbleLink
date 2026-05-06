<?php

use App\Models\User;

test('email verification routes are unavailable', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/verify-email')->assertNotFound();
    $this->actingAs($user)->post('/email/verification-notification')->assertNotFound();
    $this->actingAs($user)->get('/verify-email/1/test-hash')->assertNotFound();
});
