<?php

namespace Src\Admin\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Admin\Order\Requests\StoreOrderRequest;
use Src\Admin\Order\Requests\UpdateOrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $organizationId = auth()->guard('admin')->user()->organization_id;
        
        $query = Order::with('orderItems.product')
            ->where('organization_id', $organizationId);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->status($request->status);
        }

        // Search by customer name, order number, email, or contact numbers
        if ($request->has('search') && $request->search) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_number_one', 'like', "%{$search}%")
                  ->orWhere('contact_number_two', 'like', "%{$search}%");
            });
        }

        // Filter by lead source
        if ($request->has('lead_from') && $request->lead_from) {
            $query->where('lead_from', $request->lead_from);
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

        DB::beginTransaction();
        try {
            // Extract order items from validated data
            $orderItemsData = $validatedData['order_items'] ?? [];
            unset($validatedData['order_items']);

            // Add organization_id from authenticated admin
            $validatedData['organization_id'] = auth()->guard('admin')->user()->organization_id;

            // Create the order
            $order = Order::create($validatedData);

            // Create order items
            foreach ($orderItemsData as $itemData) {
                // Get item price if sale_amount not provided
                if (!isset($itemData['sale_amount'])) {
                    $item = Item::findOrFail($itemData['product_id']);
                    $itemData['sale_amount'] = $item->price;
                }

                $order->orderItems()->create($itemData);
            }

            $order->load('orderItems.product');

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Verify order belongs to admin's organization
        $organizationId = auth()->guard('admin')->user()->organization_id;
        if ($order->organization_id !== $organizationId) {
            return response()->json([
                'message' => 'Unauthorized access to this order'
            ], 403);
        }

        $order->load('orderItems.product');

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
        // Verify order belongs to admin's organization
        $organizationId = auth()->guard('admin')->user()->organization_id;
        if ($order->organization_id !== $organizationId) {
            return response()->json([
                'message' => 'Unauthorized access to this order'
            ], 403);
        }

        $validatedData = $request->validated();

        DB::beginTransaction();
        try {
            // Extract order items from validated data
            $orderItemsData = $validatedData['order_items'] ?? [];
            unset($validatedData['order_items']);

            // Update the order
            $order->update($validatedData);

            // Handle order items update
            if (!empty($orderItemsData)) {
                // Get existing order item IDs
                $existingItemIds = $order->orderItems()->pluck('id')->toArray();
                $updatedItemIds = [];

                foreach ($orderItemsData as $itemData) {
                    if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                        // Update existing order item
                        $orderItem = OrderItem::find($itemData['id']);
                        $orderItem->update($itemData);
                        $updatedItemIds[] = $itemData['id'];
                    } else {
                        // Create new order item
                        $newItem = $order->orderItems()->create($itemData);
                        $updatedItemIds[] = $newItem->id;
                    }
                }

                // Delete order items that were removed
                $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
                if (!empty($itemsToDelete)) {
                    OrderItem::whereIn('id', $itemsToDelete)->delete();
                }
            }

            $order->load('orderItems.product');

            DB::commit();

            return response()->json([
                'message' => 'Order updated successfully',
                'data' => $order->fresh('orderItems.product')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        // Verify order belongs to admin's organization
        $organizationId = auth()->guard('admin')->user()->organization_id;
        if ($order->organization_id !== $organizationId) {
            return response()->json([
                'message' => 'Unauthorized access to this order'
            ], 403);
        }

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
        // Verify order belongs to admin's organization
        $organizationId = auth()->guard('admin')->user()->organization_id;
        if ($order->organization_id !== $organizationId) {
            return response()->json([
                'message' => 'Unauthorized access to this order'
            ], 403);
        }

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
        $organizationId = auth()->guard('admin')->user()->organization_id;
        
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => 'Invalid status',
            ], 400);
        }

        $orders = Order::with('orderItems.product')
            ->where('organization_id', $organizationId)
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
        $organizationId = auth()->guard('admin')->user()->organization_id;
        
        // Calculate total revenue from delivered orders
        $deliveredOrders = Order::with('orderItems')
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['delivered'])
            ->get();
        
        $totalRevenue = $deliveredOrders->sum(function ($order) {
            return $order->total_amount;
        });

        $stats = [
            'total' => Order::where('organization_id', $organizationId)->count(),
            'pending' => Order::where('organization_id', $organizationId)->pending()->count(),
            'processing' => Order::where('organization_id', $organizationId)->processing()->count(),
            'shipped' => Order::where('organization_id', $organizationId)->shipped()->count(),
            'delivered' => Order::where('organization_id', $organizationId)->delivered()->count(),
            'cancelled' => Order::where('organization_id', $organizationId)->cancelled()->count(),
            'total_revenue' => $totalRevenue,
            'total_order_items' => OrderItem::whereHas('order', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })->count(),
        ];

        return response()->json([
            'message' => 'Order statistics retrieved successfully',
            'data' => $stats
        ]);
    }
}