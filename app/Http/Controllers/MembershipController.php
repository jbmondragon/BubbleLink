<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = $this->ownerOrganization($request);
        $currentRole = $this->currentRole($request);

        if (! $organization) {
            return redirect()
                ->route('organizations.create')
                ->with('warning', 'Create your organization first to manage members.');
        }

        $memberSearch = trim((string) $request->string('member_search'));
        $memberRoleFilter = (string) $request->string('member_role');

        if (! in_array($memberRoleFilter, ['', 'owner', 'manager', 'staff'], true)) {
            $memberRoleFilter = '';
        }

        $membershipsQuery = $organization->memberships()->with(['user', 'shop']);

        if ($memberSearch !== '') {
            $membershipsQuery->where(function ($query) use ($memberSearch) {
                $query->where('role', 'like', '%'.$memberSearch.'%')
                    ->orWhereHas('user', function ($userQuery) use ($memberSearch) {
                        $userQuery->where('name', 'like', '%'.$memberSearch.'%')
                            ->orWhere('email', 'like', '%'.$memberSearch.'%')
                            ->orWhere('contact_number', 'like', '%'.$memberSearch.'%');
                    });
            })->orWhereHas('shop', function ($shopQuery) use ($memberSearch) {
                $shopQuery->where('shop_name', 'like', '%'.$memberSearch.'%');
            });
        }

        if ($memberRoleFilter !== '') {
            $membershipsQuery->where('role', $memberRoleFilter);
        }

        return view('memberships.index', [
            'organization' => $organization,
            'currentRole' => $currentRole,
            'memberSearch' => $memberSearch,
            'memberRoleFilter' => $memberRoleFilter,
            'shops' => $organization->shops()->orderBy('shop_name')->get(),
            'memberships' => $membershipsQuery->orderBy('role')->latest('id')->get(),
            'memberCount' => $organization->memberships()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = $this->ownerOrganization($request);

        if (! $organization) {
            return redirect()
                ->route('organizations.create')
                ->with('warning', 'Create your organization first to manage members.');
        }

        $validated = $request->validateWithBag('membershipCreate', [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'nullable|string|max:255',
            'role' => 'required|in:manager,staff',
            'shop_id' => [
                'required',
                Rule::exists('shops', 'id')->where(fn ($query) => $query->where('organization_id', $organization->id)),
            ],
        ]);

        $resetUrl = null;

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Str::password(32),
                'contact_number' => $validated['contact_number'],
            ]);

            $token = Password::broker()->createToken($user);

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);
        }

        $membership = Membership::firstOrNew([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        if ($membership->exists && $membership->role === 'owner') {
            return back()
                ->withErrors([
                    'email' => 'That user already owns this organization.',
                ], 'membershipCreate')
                ->withInput();
        }

        $membership->fill([
            'role' => $validated['role'],
            'shop_id' => $validated['shop_id'],
        ])->save();

        $redirect = redirect()->route('memberships.index')->with('success', 'Member added!');

        if ($resetUrl) {
            $redirect->with('memberInvite', [
                'name' => $user->name,
                'email' => $user->email,
                'reset_url' => $resetUrl,
            ]);
        }

        return $redirect;
    }

    public function destroy(Request $request, Membership $membership): RedirectResponse
    {
        $organization = $this->ownerOrganization($request);

        abort_unless($membership->organization_id === $organization->id, 403);
        abort_if($membership->role === 'owner', 403);

        $membership->delete();

        return redirect()->route('memberships.index')->with('success', 'Member removed!');
    }

    public function update(Request $request, Membership $membership): RedirectResponse
    {
        $organization = $this->ownerOrganization($request);

        abort_unless($membership->organization_id === $organization->id, 403);
        abort_if($membership->role === 'owner', 403);

        $validated = $request->validateWithBag('membershipUpdate-'.$membership->id, [
            'membership_id' => 'required|integer|in:'.$membership->id,
            'role' => 'required|in:manager,staff',
            'shop_id' => [
                'required',
                Rule::exists('shops', 'id')->where(fn ($query) => $query->where('organization_id', $organization->id)),
            ],
        ]);

        $membership->update([
            'role' => $validated['role'],
            'shop_id' => $validated['shop_id'],
        ]);

        return redirect()->route('memberships.index')->with('success', 'Member role updated!');
    }
}
