<?php

namespace Src\Admin\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Http\Request;
use Src\Admin\Order\Requests\StoreOrderRequest;
use Src\Admin\Order\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        // Debug: Check authentication
        $user = $request->user();



        $query = Order::with('item');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->status($request->status);
        }

        // Search by name, order number, email, or phone
        if ($request->has('search') && $request->search) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
        $validatedData = $request->validated();

        // Get item to calculate price if not provided
        if (!isset($validatedData['price'])) {
            $item = Item::findOrFail($validatedData['item_id']);
            $validatedData['price'] = $item->price;
        }

        $order = Order::create($validatedData);
        $order->load('item');

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load('item');

        return response()->json([
            'message' => 'Order retrieved successfully',
            'data' => $order
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validatedData = $request->validated();

        $order->update($validatedData);
        $order->load('item');

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $order->fresh()
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => $order->fresh()
        ]);
    }

    /**
     * Get orders by status.
     */
    public function byStatus(Request $request, $status)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => 'Invalid status',
            ], 400);
        }

        $orders = Order::with('item')
            ->status($status)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => ucfirst($status) . ' orders retrieved successfully',
            'data' => $orders
        ]);
    }

    /**
     * Get order statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => Order::count(),
            'pending' => Order::pending()->count(),
            'processing' => Order::processing()->count(),
            'shipped' => Order::shipped()->count(),
            'delivered' => Order::delivered()->count(),
            'cancelled' => Order::cancelled()->count(),
            'total_revenue' => Order::whereIn('status', ['delivered'])->sum('price'),
        ];

        return response()->json([
            'message' => 'Order statistics retrieved successfully',
            'data' => $stats
        ]);
    }
}
