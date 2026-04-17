<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shop_services = \App\Models\ShopService::all();
        return view('shop_services.index', compact('shop_services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('shop_services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'service_id' => 'required|exists:services,id',
            'price' => 'required|numeric',
        ]);
        \App\Models\ShopService::create($validated);
        return redirect()->route('shop_services.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shop_service = \App\Models\ShopService::findOrFail($id);
        return view('shop_services.edit', compact('shop_service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop_service = \App\Models\ShopService::findOrFail($id);
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'service_id' => 'required|exists:services,id',
            'price' => 'required|numeric',
        ]);
        $shop_service->update($validated);
        return redirect()->route('shop_services.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop_service = \App\Models\ShopService::findOrFail($id);
        $shop_service->delete();
        return redirect()->route('shop_services.index');
    }
}
