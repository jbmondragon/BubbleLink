<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ShopServiceController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('shopServiceCreate', [
            'shop_id' => 'required|exists:shops,id',
            'service_id' => [
                'required',
                Rule::unique('shop_services')->where(fn ($query) => $query->where('shop_id', $request->integer('shop_id'))),
            ],
            'price' => 'required|numeric|min:0',
        ]);

        $shop = Shop::findOrFail($validated['shop_id']);
        Gate::authorize('create', [ShopService::class, $shop]);

        $serviceExistsForOrganization = Service::query()
            ->whereKey($validated['service_id'])
            ->where('organization_id', $shop->organization_id)
            ->exists();

        if (! $serviceExistsForOrganization) {
            return back()
                ->withErrors([
                    'service_id' => 'Select a service from your organization.',
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
