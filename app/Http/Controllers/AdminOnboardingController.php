<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOnboardingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->is_platform_admin) {
            return redirect()->route('platform-admin.owner-registrations.index');
        }

        if ($request->user()->memberships()->exists()) {
            return redirect()->route('dashboard');
        }

        if ($request->user()->isPendingShopOwnerApproval()) {
            return redirect()
                ->route('customer.shops.index')
                ->with('warning', 'Your shop owner registration is still pending approval.');
        }

        if ($request->user()->isRejectedShopOwnerRegistration()) {
            return redirect()
                ->route('customer.shops.index')
                ->with('warning', 'Your shop owner registration was rejected. Please contact the platform admin.');
        }

        abort_unless($request->user()->isApprovedShopOwnerRegistration(), 403);

        return view('admin.start');
    }
}
