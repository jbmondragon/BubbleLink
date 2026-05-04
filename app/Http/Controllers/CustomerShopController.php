<?php

namespace App\Http\Controllers;

/**
 * Handles customer-facing shop discovery, filtering, and shop detail
 * rendering.
 */

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerShopController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $shops = Shop::query()
            ->with(['shopServices.service'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('shop_name', 'like', '%'.$search.'%')
                        ->orWhere('address', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhereHas('shopServices.service', function ($serviceQuery) use ($search) {
                            $serviceQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderBy('shop_name')
            ->get();

        $shopCards = $shops->map(function (Shop $shop) {
            $featuredServices = $shop->shopServices
                ->sortBy(fn ($shopService) => $shopService->service->name)
                ->take(3)
                ->values();

            return [
                'shop' => $shop,
                'serviceCount' => $shop->shopServices->count(),
                'featuredServices' => $featuredServices,
                'startingPrice' => $shop->shopServices->min('price'),
            ];
        });

        return view('customer.shops.index', [
            'shopCards' => $shopCards,
            'search' => $search,
        ]);
    }

    public function show(Shop $shop): View
    {
        $shop = Shop::query()
            ->with(['shopServices.service'])
            ->findOrFail($shop->id);

        $services = $shop->shopServices
            ->sortBy(fn ($shopService) => $shopService->service->name)
            ->values()
            ->map(fn ($shopService) => [
                'shopService' => $shopService,
                'bookingSummary' => 'Available for pickup, delivery, or both',
            ]);

        return view('customer.shops.show', [
            'shop' => $shop,
            'services' => $services,
            'serviceCount' => $services->count(),
        ]);
    }
}
