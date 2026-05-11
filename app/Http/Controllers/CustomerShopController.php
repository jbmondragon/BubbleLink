<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Implements SID2 and SID3 for customer shop discovery.
 *
 * Responsibilities:
 * - SID2: Browse and search available laundry shops
 * - SID3: View shop details and offered services before ordering
 */
class CustomerShopController extends Controller
{

    public function index(Request $request): View // Allows users to find laundry services usign the search
    {
        $search = trim($request->input('search', '')); //Gets search term from request, trims whitespace, defaults to empty string

        $shops = Shop::with('shopServices.service') // Eager load shop services and their associated services
            ->when($search, function ($q) use ($search) { //Only apply search filters if search term is not empty
                $q->where('shop_name', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('shopServices.service', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            })
            ->orderBy('shop_name') //orders indexes alphabetically
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
        $shop->load('shopServices.service'); //Loads shop services with service details

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