<?php

namespace Src\Admin\Item\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Item::query();

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by in stock
        if ($request->has('in_stock') && $request->boolean('in_stock')) {
            $query->inStock();
        }

        // Search by name or code
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $items = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Items retrieved successfully',
            'data' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255|unique:items,code',
                'description' => 'nullable|string',
                'length' => 'required|numeric|min:0',
                'width' => 'required|numeric|min:0',
                'height' => 'required|numeric|min:0',
                'weight_limit' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'is_active' => 'boolean',
                'material' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'stock_quantity' => 'required|integer|min:0',
            ]);

            $item = Item::create($validatedData);

            return response()->json([
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return response()->json([
            'message' => 'Item retrieved successfully',
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|max:255|unique:items,code,' . $item->id,
                'description' => 'nullable|string',
                'length' => 'sometimes|required|numeric|min:0',
                'width' => 'sometimes|required|numeric|min:0',
                'height' => 'sometimes|required|numeric|min:0',
                'weight_limit' => 'sometimes|required|numeric|min:0',
                'price' => 'sometimes|required|numeric|min:0',
                'is_active' => 'boolean',
                'material' => 'nullable|string|max:255',
                'color' => 'nullable|string|max:255',
                'stock_quantity' => 'sometimes|required|integer|min:0',
            ]);

            $item->update($validatedData);

            return response()->json([
                'message' => 'Item updated successfully',
                'data' => $item->fresh()
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully'
        ]);
    }

    /**
     * Get available items (active and in stock).
     */
    public function available()
    {
        $items = Item::active()->inStock()->get();

        return response()->json([
            'message' => 'Available items retrieved successfully',
            'data' => $items
        ]);
    }

    /**
     * Toggle item active status.
     */
    public function toggleActive(Item $item)
    {
        $item->update(['is_active' => !$item->is_active]);

        return response()->json([
            'message' => 'Item status updated successfully',
            'data' => $item->fresh()
        ]);
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(Request $request, Item $item)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0'
        ]);

        $item->update(['stock_quantity' => $request->stock_quantity]);

        return response()->json([
            'message' => 'Stock quantity updated successfully',
            'data' => $item->fresh()
        ]);
    }
}