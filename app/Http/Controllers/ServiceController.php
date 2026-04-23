<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = $this->currentOrganization($request);
        $membership = $this->currentMembership($request);
        $currentRole = $this->currentRole($request);

        if (! $organization) {
            return redirect()
                ->route('organizations.create')
                ->with('warning', 'Create your organization first to manage services.');
        }

        if ($currentRole !== 'manager') {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Only managers can manage services. Owners can manage shops and member roles from the dashboard.');
        }

        $shops = $organization->shops()
            ->whereKey($membership?->shop_id ?? 0)
            ->with('shopServices.service')
            ->get();

        return view('services.index', [
            'organization' => $organization,
            'currentMembership' => $membership,
            'currentRole' => $currentRole,
            'shops' => $shops,
            'services' => $organization->services()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = $this->currentOrganization($request);
        $membership = $this->currentMembership($request);
        $currentRole = $this->currentRole($request);

        if (! $organization) {
            return redirect()
                ->route('organizations.create')
                ->with('warning', 'Create your organization first to manage services.');
        }

        if ($currentRole !== 'manager') {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Only managers can manage services. Owners can manage shops and member roles from the dashboard.');
        }

        abort_unless($membership?->shop_id, 403);

        $validated = $request->validateWithBag('serviceCreate', [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'name')->where(
                    fn ($query) => $query->where('organization_id', $organization->id)
                ),
            ],
        ]);

        Service::create([
            ...$validated,
            'organization_id' => $organization->id,
        ]);

        return redirect()->route('services.index')->with('success', 'Service created!');
    }
}
