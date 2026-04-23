<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerShopController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $shops = Shop::query()
            ->with(['organization', 'shopServices.service'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('shop_name', 'like', '%'.$search.'%')
                        ->orWhere('address', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('shop_name')
            ->get();

        return view('customer.shops.index', [
            'shops' => $shops,
            'search' => $search,
            'demoAccounts' => [
                'customers' => [
                    ['label' => 'Customer Demo', 'email' => 'bob@example.com', 'password' => 'password', 'description' => 'Use this to browse shops and review seeded customer orders.'],
                    ['label' => 'Second Customer', 'email' => 'mia@example.com', 'password' => 'password', 'description' => 'Use this to test a different customer order history.'],
                ],
                'admins' => [
                    ['label' => 'Owner Demo', 'email' => 'john@example.com', 'password' => 'password', 'description' => 'Owner of QuickClean Laundry and manager in FreshFold Laundry for switcher testing.'],
                    ['label' => 'Manager Demo', 'email' => 'alice@example.com', 'password' => 'password', 'description' => 'Manager in QuickClean Laundry.'],
                    ['label' => 'Owner Demo 2', 'email' => 'jane@example.com', 'password' => 'password', 'description' => 'Owner of FreshFold Laundry.'],
                    ['label' => 'Staff Demo', 'email' => 'mark@example.com', 'password' => 'password', 'description' => 'Staff account in FreshFold Laundry.'],
                ],
            ],
        ]);
    }

    public function show(Shop $shop): View
    {
        $shop->load(['organization', 'shopServices.service']);

        return view('customer.shops.show', [
            'shop' => $shop,
            'services' => $shop->shopServices->sortBy(fn ($shopService) => $shopService->service->name)->values(),
        ]);
    }
}
