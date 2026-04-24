<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\Organization;
use App\Models\User;

class MembershipPolicy
{
    public function viewAny(User $user, Organization $organization): bool
    {
        return $this->ownsOrganization($user, $organization->id);
    }

    public function create(User $user, Organization $organization): bool
    {
        return $this->ownsOrganization($user, $organization->id);
    }

    public function update(User $user, Membership $membership): bool
    {
        return $membership->role !== 'owner'
            && $this->ownsOrganization($user, $membership->organization_id);
    }

    public function delete(User $user, Membership $membership): bool
    {
        return $membership->role !== 'owner'
            && $this->ownsOrganization($user, $membership->organization_id);
    }

    private function ownsOrganization(User $user, int $organizationId): bool
    {
        return $user->memberships()
            ->where('organization_id', $organizationId)
            ->where('role', 'owner')
            ->exists();
    }
}
