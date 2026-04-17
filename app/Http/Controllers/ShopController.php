<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = \App\Models\Shop::all();
        return view('shops.index', compact('shops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('shops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_name' => 'required',
            'email' => 'required|email|unique:shops,email',
            'password' => 'required',
            'address' => 'required',
            'contact_number' => 'nullable',
            'description' => 'nullable',
        ]);
        $validated['password'] = bcrypt($validated['password']);
        \App\Models\Shop::create($validated);
        return redirect()->route('shops.index');
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
        $shop = \App\Models\Shop::findOrFail($id);
        return view('shops.edit', compact('shop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = \App\Models\Shop::findOrFail($id);
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shop_name' => 'required',
            'email' => 'required|email|unique:shops,email,' . $shop->id,
            'address' => 'required',
            'contact_number' => 'nullable',
            'description' => 'nullable',
        ]);
        $shop->update($validated);
        return redirect()->route('shops.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = \App\Models\Shop::findOrFail($id);
        $shop->delete();
        return redirect()->route('shops.index');
    }
}
