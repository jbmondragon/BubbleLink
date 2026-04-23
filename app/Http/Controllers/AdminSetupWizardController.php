<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSetupWizardController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $organization = $this->ownerOrganization($request);

        if (! $organization) {
            return redirect()
                ->route('admin.start')
                ->with('warning', 'Create your organization first before continuing the guided setup.');
        }

        $shops = $organization->shops()->orderBy('shop_name')->get();
        $services = $organization->services()->orderBy('name')->get();
        $memberships = $organization->memberships()->with('user')->orderBy('role')->latest('id')->get();

        return view('admin.setup', [
            'organization' => $organization,
            'shops' => $shops,
            'services' => $services,
            'memberships' => $memberships,
            'hasShop' => $shops->isNotEmpty(),
            'hasService' => $services->isNotEmpty(),
            'hasExtraMember' => $memberships->where('role', '!=', 'owner')->isNotEmpty(),
        ]);
    }

    public function storeShop(Request $request): RedirectResponse
    {
        $organization = $this->ownerOrganization($request);
        abort_unless($organization, 403);

        $validated = $request->validateWithBag('setupShop', [
            'shop_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Shop::create([
            ...$validated,
            'organization_id' => $organization->id,
        ]);

        return redirect()->route('admin.setup')->with('success', 'First shop created. Continue with your first service.');
    }

    public function storeService(Request $request): RedirectResponse
    {
        $organization = $this->ownerOrganization($request);
        abort_unless($organization, 403);

        $validated = $request->validateWithBag('setupService', [
            'shop_id' => [
                'required',
                Rule::exists('shops', 'id')->where(fn ($query) => $query->where('organization_id', $organization->id)),
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'name')->where(fn ($query) => $query->where('organization_id', $organization->id)),
            ],
            'price' => 'required|numeric|min:0.01',
        ]);

        $service = Service::create([
            'organization_id' => $organization->id,
            'name' => $validated['name'],
        ]);

        ShopService::create([
            'shop_id' => $validated['shop_id'],
            'service_id' => $service->id,
            'price' => $validated['price'],
        ]);

        return redirect()->route('admin.setup')->with('success', 'First service created and assigned to your shop. Add a teammate next.');
    }

    public function storeMember(Request $request): RedirectResponse
    {
        $organization = $this->ownerOrganization($request);
        abort_unless($organization, 403);

        $validated = $request->validateWithBag('setupMember', [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'nullable|string|max:255',
            'role' => 'required|in:manager,staff',
            'shop_id' => [
                'required',
                Rule::exists('shops', 'id')->where(fn ($query) => $query->where('organization_id', $organization->id)),
            ],
        ]);

        $user = User::query()->firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'contact_number' => $validated['contact_number'],
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        if (! $user->contact_number && filled($validated['contact_number'])) {
            $user->update(['contact_number' => $validated['contact_number']]);
        }

        $membership = Membership::firstOrNew([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        if ($membership->exists && $membership->role === 'owner') {
            return back()
                ->withErrors([
                    'email' => 'That user already owns this organization.',
                ], 'setupMember')
                ->withInput();
        }

        $membership->fill([
            'role' => $validated['role'],
            'shop_id' => $validated['shop_id'],
        ])->save();

        return redirect()->route('admin.setup')->with('success', 'Team member added. Your guided setup is complete.');
    }
}
