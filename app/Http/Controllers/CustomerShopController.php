<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerShopController extends Controller
{
    // List all shops (with search): User Story 2 and 3
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));

        $shops = Shop::with('shopServices.service')
            ->when($search, function ($q) use ($search) {
                $q->where('shop_name', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('shopServices.service', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            })
            ->orderBy('shop_name')
            ->get();

        $shopCards = $shops->map(function ($shop) {
            return [
                'shop' => $shop,
                'serviceCount' => $shop->shopServices->count(),
                'featuredServices' => $shop->shopServices
                    ->sortBy('service.name')
                    ->take(3)
                    ->values(),
                'startingPrice' => $shop->shopServices->min('price'),
            ];
        });

        return view('customer.shops.index', compact('shopCards', 'search'));
    }

    // Show single shop details
    public function show(Shop $shop): View
    {
        $shop->load('shopServices.service');

        $services = $shop->shopServices
            ->sortBy('service.name')
            ->values()
            ->map(fn ($s) => [
                'shopService' => $s,
                'bookingSummary' => 'Available for pickup, delivery, or both',
            ]);

        return view('customer.shops.show', [
            'shop' => $shop,
            'services' => $services,
            'serviceCount' => $services->count(),
        ]);
    }
}