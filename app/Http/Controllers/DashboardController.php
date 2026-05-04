<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->user()->is_platform_admin) {
            return redirect()->route('platform-admin.owner-registrations.index');
        }

        $shops = $this->ownerShops($request)->with([
            'orders',
            'shopServices',
        ])->get();

        return view('dashboard', [
            'shops' => $shops,
            'shopCount' => $shops->count(),
            'totalOrders' => $shops->flatMap->orders->count(),
            'totalRevenue' => $shops->flatMap->orders->sum('total_price'),
            'assignedServiceCount' => $shops->flatMap->shopServices->count(),
        ]);
    }
}
