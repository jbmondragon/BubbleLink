<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function create(): View
    {
        return view('organizations.create', [
            'guided' => request()->boolean('guided'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guided' => 'nullable|boolean',
        ]);

        $user = $request->user();

        $organization = Organization::create([
            'name' => $request->name,
            'owner_user_id' => $user->id,
        ]);

        Membership::create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'role' => 'owner',
        ]);

        $request->session()->put('current_organization_id', $organization->id);

        if ($request->boolean('guided')) {
            return redirect()->route('admin.setup')->with('success', 'Organization created. Continue the guided setup below.');
        }

        return redirect()->route('dashboard')->with('success', 'Organization created!');
    }

    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|integer|exists:organizations,id',
        ]);

        abort_unless(
            $request->user()->memberships()->where('organization_id', $validated['organization_id'])->exists(),
            403
        );

        $request->session()->put('current_organization_id', (int) $validated['organization_id']);

        return redirect()->route('dashboard')->with('success', 'Active organization switched.');
    }
}
