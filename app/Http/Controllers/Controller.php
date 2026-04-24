<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Organization;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function ensureCustomer(Request $request): void
    {
        $user = $request->user();

        abort_unless(
            ! $user->is_platform_admin
            && ! $user->memberships()->exists()
            && $user->owner_registration_status === null,
            403
        );
    }

    protected function ensureOrganizationCreator(Request $request): void
    {
        $user = $request->user();

        abort_if($user->is_platform_admin, 403);

        if ($user->memberships()->where('role', 'owner')->exists()) {
            return;
        }

        abort_unless(
            $user->isApprovedShopOwnerRegistration() && ! $user->memberships()->exists(),
            403
        );
    }

    protected function currentMembership(Request $request): ?Membership
    {
        $memberships = $request->user()
            ->memberships()
            ->with(['organization', 'shop'])
            ->orderByRaw("case role when 'owner' then 1 when 'manager' then 2 when 'staff' then 3 else 4 end")
            ->get();

        if ($memberships->isEmpty()) {
            return null;
        }

        $selectedOrganizationId = $request->session()->get('current_organization_id');

        if ($selectedOrganizationId !== null) {
            $selectedMembership = $memberships->firstWhere('organization_id', (int) $selectedOrganizationId);

            if ($selectedMembership) {
                return $selectedMembership;
            }

            $request->session()->forget('current_organization_id');
        }

        return $memberships->first();
    }

    protected function currentOrganization(Request $request): ?Organization
    {
        return $this->currentMembership($request)?->organization;
    }

    protected function currentRole(Request $request): ?string
    {
        return $this->currentMembership($request)?->role;
    }

    protected function organizationForRoles(Request $request, array $roles): ?Organization
    {
        $membership = $this->currentMembership($request);

        if (! $membership) {
            return null;
        }

        abort_unless(in_array($membership->role, $roles, true), 403);

        return $membership->organization;
    }

    protected function ownerOrganization(Request $request): ?Organization
    {
        return $this->organizationForRoles($request, ['owner']);
    }
}
