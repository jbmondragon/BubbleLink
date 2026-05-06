<?php

/**
 * Dashboard Controller
 *
 * Acts as the main entry point for authenticated users and dynamically
 * redirects or displays dashboard data based on user role.
 *
 * Responsibilities:
 *
 * 1. Role-Based Redirection:
 *    - Platform administrators are redirected to the owner approval dashboard
 *    - Other users proceed to role-specific dashboard view rendering
 *
 * 2. Business Owner Dashboard Data Aggregation:
 *    - Retrieves all shops owned by the authenticated user
 *    - Loads related orders and shop services in a single query set
 *    - Computes aggregated metrics:
 *        - Total shops owned
 *        - Total orders across all shops
 *        - Total revenue from all orders
 *        - Total assigned services across shops
 *
 * Design Notes:
 * - Uses a single-action controller (__invoke) for simplicity
 * - Leverages eager loading to optimize performance and reduce queries
 * - Performs in-memory aggregation for dashboard summary statistics
 * - Centralizes role-based dashboard routing logic in one entry point
 */

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
