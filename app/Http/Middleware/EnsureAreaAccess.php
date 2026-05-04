<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class EnsureAreaAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $area): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $hasShops = $user->shops()->exists();
        $isApprovedShopOwner = $user->isApprovedShopOwnerRegistration();

        $authorized = match ($area) {
            'customer' => ! $user->is_platform_admin && $user->owner_registration_status === null,
            'platform-admin' => $user->is_platform_admin,
            'business' => ! $user->is_platform_admin && ($hasShops || $isApprovedShopOwner),
            'dashboard' => $user->is_platform_admin || (! $user->is_platform_admin && ($hasShops || $isApprovedShopOwner)),
            default => throw new InvalidArgumentException('Unknown area access middleware segment ['.$area.'].'),
        };

        abort_unless($authorized, 403);

        return $next($request);
    }
}
