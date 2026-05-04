<?php

/**
 * Base controller for shared customer-authorization and owner shop-scoping
 * helpers used across application controllers.
 */

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function ensureCustomer(Request $request): void
    {
        $user = $request->user();

        abort_unless(
            ! $user->is_platform_admin
            && $user->owner_registration_status === null,
            403
        );
    }

    protected function ownerShops(Request $request)
    {
        return Shop::where('owner_user_id', $request->user()->id);
    }
}
