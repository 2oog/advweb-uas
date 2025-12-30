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
    public function index(Request $request)
    {
        $query = Order::with('orderItems');

        if ($request->has('month') && $request->has('year')) {
            $query->whereYear('order_date', $request->year)
                  ->whereMonth('order_date', $request->month);
        }

        if ($request->has('sort_by')) {
            $sortDir = $request->get('sort_dir', 'desc');
            $query->orderBy($request->sort_by, $sortDir);
        } else {
            // Default sort
            $query->orderBy('order_date', 'desc');
        }

        return $query->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'table_number' => 'required|string',
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
                'table_number' => $validated['table_number'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'PAID',
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
        // Just for updating payment status
        $order = Order::findOrFail($id);
        $order->update($request->only(['payment_status']));

        return $order;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // No deleting orders :3, but here for completeness
        // Order::destroy($id);
        // return response()->noContent();
    }
}
