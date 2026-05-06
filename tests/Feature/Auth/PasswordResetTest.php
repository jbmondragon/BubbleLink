<?php

test('password reset routes are unavailable', function () {
    $this->get('/forgot-password')->assertNotFound();
    $this->post('/forgot-password', ['email' => 'test@example.com'])->assertNotFound();
    $this->get('/reset-password/test-token')->assertNotFound();
    $this->post('/reset-password', [
        'token' => 'test-token',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();
});
