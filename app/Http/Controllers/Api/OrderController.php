<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::with('orderItems')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['id']);
                $itemSubtotal = $menuItem->price * $item['quantity'];

                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'menu_name' => $menuItem->name,
                    'quantity' => $item['quantity'],
                    'price_at_time' => $menuItem->price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $taxAmount = (int) round($subtotal * 0.1);  // 10% tax
            $totalAmount = $subtotal + $taxAmount;

            $order = Order::create([
                'order_date' => now(),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'PAID',  // Default to PAID as per mockup flow usually implies immediate payment
            ]);

            $order->orderItems()->createMany($orderItemsData);

            return $order->load('orderItems');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Order::with('orderItems')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Usually orders are not updated after payment, but here for completeness
        // Only allow updating status maybe?
        $order = Order::findOrFail($id);
        $order->update($request->only(['payment_status']));
        return $order;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Order::destroy($id);
        return response()->noContent();
    }
}
