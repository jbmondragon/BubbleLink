<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $shops = $this->ownerShops($request)->with('shopServices.service')->get();

        if ($shops->isEmpty()) {
            return redirect()
                ->route('shops.create')
                ->with('warning', 'Create your first shop before managing services.');
        }

        $shops->each(fn (Shop $shop) => Service::ensureDefaultServicesForShop($shop));

        $services = Service::whereIn('shop_id', $shops->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('services.index', [
            'shops' => $shops,
            'services' => $services,
        ]);
    }
}
