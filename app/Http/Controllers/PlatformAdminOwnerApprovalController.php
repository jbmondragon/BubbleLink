<?php

namespace App\Http\Controllers;

use App\Models\OwnerRegistrationReview;
use App\Models\User;
use App\Notifications\ShopOwnerRegistrationApprovedNotification;
use App\Notifications\ShopOwnerRegistrationRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformAdminOwnerApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensurePlatformAdmin($request);

        $ownerRegistrations = User::query()
            ->where('is_platform_admin', false)
            ->whereNotNull('owner_registration_status')
            ->with('approvedBy')
            ->orderByRaw("case owner_registration_status when 'pending' then 1 when 'rejected' then 2 when 'approved' then 3 else 4 end")
            ->latest('created_at')
            ->get();

        return view('platform-admin.owner-registrations.index', [
            'pendingOwnerRegistrations' => $ownerRegistrations->where('owner_registration_status', 'pending')->values(),
            'reviewedOwnerRegistrations' => $ownerRegistrations->where('owner_registration_status', '!=', 'pending')->values(),
            'ownerRegistrationReviews' => OwnerRegistrationReview::query()
                ->with(['shopOwner', 'platformAdmin'])
                ->latest('id')
                ->limit(20)
                ->get(),
        ]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $this->ensurePlatformAdmin($request);
        $this->ensureReviewableOwnerRegistration($user);

        $this->recordDecision($request->user(), $user, 'approved');
        $user->notify(new ShopOwnerRegistrationApprovedNotification);

        return redirect()
            ->route('platform-admin.owner-registrations.index')
            ->with('success', 'Shop owner registration approved.');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->ensurePlatformAdmin($request);
        $this->ensureReviewableOwnerRegistration($user);

        $this->recordDecision($request->user(), $user, 'rejected');
        $user->notify(new ShopOwnerRegistrationRejectedNotification);

        return redirect()
            ->route('platform-admin.owner-registrations.index')
            ->with('success', 'Shop owner registration rejected.');
    }

    private function ensurePlatformAdmin(Request $request): void
    {
        abort_unless($request->user()->is_platform_admin, 403);
    }

    private function ensureReviewableOwnerRegistration(User $user): void
    {
        abort_if($user->is_platform_admin, 404);
        abort_unless($user->owner_registration_status !== null, 404);
    }

    private function recordDecision(User $platformAdmin, User $shopOwner, string $newStatus): void
    {
        $previousStatus = $shopOwner->owner_registration_status;

        $shopOwner->update([
            'owner_registration_status' => $newStatus,
            'approved_by_user_id' => $platformAdmin->id,
            'owner_registration_reviewed_at' => now(),
        ]);

        OwnerRegistrationReview::create([
            'shop_owner_user_id' => $shopOwner->id,
            'platform_admin_user_id' => $platformAdmin->id,
            'action' => $newStatus,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
        ]);
    }
}
