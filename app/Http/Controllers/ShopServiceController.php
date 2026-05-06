<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;


/**
 * ShopServiceController
 *
 * Manages the assignment of services to shops:
 * - Adds (assigns) a service to a specific shop with max load weight and pricing
 * - Ensures services are unique per shop
 * - Validates that services belong to the correct shop
 * - Removes assigned services from a shop
 *
 * Implements SID7 for shop owners managing services and pricing.
 */

class ShopServiceController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $shop = Shop::findOrFail($request->integer('shop_id'));
        Gate::authorize('create', [ShopService::class, $shop]);

        Service::ensureDefaultServicesForShop($shop);

        $validated = $request->validateWithBag('shopServiceCreate', [
            'shop_id' => 'required|exists:shops,id',
            'service_id' => [
                'required',
                Rule::unique('shop_services')->where(fn ($query) => $query->where('shop_id', $request->integer('shop_id'))),
            ],
            'price' => 'required|numeric|min:0',
            'max_weight_kg' => 'required|numeric|min:0',
        ]);

        $serviceExistsForShop = Service::query()
            ->whereKey($validated['service_id'])
            ->where('shop_id', $shop->id)
            ->exists();

        if (! $serviceExistsForShop) {
            return back()
                ->withErrors([
                    'service_id' => 'Select a service from your shop.',
                ], 'shopServiceCreate')
                ->withInput();
        }

        ShopService::create($validated);

        return redirect()->route('services.index')->with('success', 'Service assigned to shop!');
    }

    public function destroy(Request $request, ShopService $shopService): RedirectResponse
    {
        Gate::authorize('delete', $shopService);

        $shopService->delete();

        return redirect()->route('services.index')->with('success', 'Shop service removed!');
    }
}
