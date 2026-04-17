<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = \App\Models\Order::all();
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops,id',
            'service_id' => 'required|exists:services,id',
            'service_mode' => 'required',
            'pickup_address' => 'nullable',
            'delivery_address' => 'nullable',
            'pickup_datetime' => 'nullable|date',
            'total_price' => 'required|numeric',
            'status' => 'required',
            'payment_status' => 'required',
        ]);
        \App\Models\Order::create($validated);
        return redirect()->route('orders.index');
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
        $order = \App\Models\Order::findOrFail($id);
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'shop_id' => 'required|exists:shops,id',
            'service_id' => 'required|exists:services,id',
            'service_mode' => 'required',
            'pickup_address' => 'nullable',
            'delivery_address' => 'nullable',
            'pickup_datetime' => 'nullable|date',
            'total_price' => 'required|numeric',
            'status' => 'required',
            'payment_status' => 'required',
        ]);
        $order->update($validated);
        return redirect()->route('orders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();
        return redirect()->route('orders.index');
    }
}
