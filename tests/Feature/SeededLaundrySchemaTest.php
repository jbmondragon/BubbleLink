<?php

use App\Models\Order;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads the seeded owner managed laundry graph through eloquent relationships', function () {
    $this->seed();

    expect(User::query()->whereNotNull('email_verified_at')->count())->toBe(3);

    $quickCleanOwner = User::query()->with('shops.shopServices.service')->findOrFail(1);

    expect($quickCleanOwner->email)->toBe('john@example.com');
    expect($quickCleanOwner->shops)->toHaveCount(1);
    expect($quickCleanOwner->shops->first()->contact_number)->toBe('0287001001');
    expect($quickCleanOwner->shops->first()->shopServices)->toHaveCount(1);
    expect($quickCleanOwner->shops->first()->shopServices->first()->service->name)->toBe('wash, dry, fold');

    $platformAdmin = User::query()->findOrFail(3);

    expect($platformAdmin->email)->toBe('admin@bubblelink.test');
    expect($platformAdmin->is_platform_admin)->toBeTrue();

    expect(Shop::query()->count())->toBe(1);
    expect(Service::query()->count())->toBe(1);
    expect(Service::query()->where('name', 'wash, dry, fold')->count())->toBe(1);

    $order = Order::query()
        ->with(['customer', 'shop', 'shopService.service'])
        ->findOrFail(1);

    expect($order->customer->email)->toBe('bob@example.com');
    expect($order->shop->shop_name)->toBe('QuickClean Manila');
    expect($order->shopService->price)->toBe('100.00');
    expect($order->shopService->service->name)->toBe('wash, dry, fold');
    expect($order->status)->toBe('pending');
    expect($order->service_mode)->toBe('both');
    expect($order->payment_status)->toBe('unpaid');
    expect(Order::query()->count())->toBe(1);
});
