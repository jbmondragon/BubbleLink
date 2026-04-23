<?php

use App\Models\Order;
use App\Models\Organization;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads the seeded laundry business graph through eloquent relationships', function () {
    $this->seed();

    expect(User::query()->whereNotNull('email_verified_at')->count())->toBe(6);

    $organization = Organization::query()
        ->with(['owner', 'memberships.user', 'shops.shopServices.service'])
        ->findOrFail(1);

    expect($organization->name)->toBe('QuickClean Laundry');
    expect($organization->owner->email)->toBe('john@example.com');
    expect($organization->memberships)->toHaveCount(2);
    expect($organization->shops)->toHaveCount(2);
    expect($organization->services)->toHaveCount(4);
    expect($organization->shops->first()->contact_number)->toBe('0287001001');
    expect($organization->shops->first()->shopServices)->toHaveCount(2);
    expect($organization->shops->first()->shopServices->first()->service->name)->toBe('wash, dry, fold');

    expect(Service::query()->where('organization_id', $organization->id)->count())->toBe(4);

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

    $customer = User::query()->with('orders')->findOrFail(3);

    expect($customer->orders)->toHaveCount(2);
    expect($customer->contact_number)->toBe('09170000003');

    $secondOrganization = Organization::query()
        ->with(['owner', 'memberships.user', 'shops.shopServices.service'])
        ->findOrFail(2);

    expect($secondOrganization->name)->toBe('FreshFold Laundry');
    expect($secondOrganization->owner->email)->toBe('jane@example.com');
    expect($secondOrganization->memberships)->toHaveCount(3);
    expect($secondOrganization->shops)->toHaveCount(1);
    expect($secondOrganization->services)->toHaveCount(2);
    expect($secondOrganization->shops->first()->shopServices)->toHaveCount(2);
    expect($secondOrganization->shops->first()->shopServices->first()->service->organization_id)->toBe($secondOrganization->id);

    expect(Service::query()->where('organization_id', $secondOrganization->id)->count())->toBe(2);
    expect(Service::query()->where('name', 'wash, dry, fold')->count())->toBe(2);

    $secondOrder = Order::query()
        ->with(['customer', 'shop', 'shopService.service'])
        ->findOrFail(2);

    expect($secondOrder->customer->email)->toBe('mia@example.com');
    expect($secondOrder->shop->shop_name)->toBe('FreshFold Davao');
    expect($secondOrder->shopService->price)->toBe('125.00');
    expect($secondOrder->shopService->service->organization_id)->toBe($secondOrganization->id);
    expect($secondOrder->status)->toBe('completed');
    expect($secondOrder->payment_status)->toBe('paid');

    $latestBobOrder = Order::query()
        ->with(['customer', 'shop'])
        ->findOrFail(3);

    expect($latestBobOrder->customer->email)->toBe('bob@example.com');
    expect($latestBobOrder->shop->shop_name)->toBe('QuickClean Cebu');
    expect($latestBobOrder->status)->toBe('accepted');
    expect($latestBobOrder->service_mode)->toBe('pickup_only');

    $multiOrganizationUser = User::query()->with('memberships')->findOrFail(1);

    expect($multiOrganizationUser->memberships)->toHaveCount(2);
    expect($multiOrganizationUser->memberships->pluck('organization_id')->sort()->values()->all())->toBe([1, 2]);
});
