<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // GET /api/shopping-lists/{id}/items
    public function index(string $shoppingListId)
    {
        $items = Item::where('shopping_list_id', $shoppingListId)->get();

        return response()->json([
            'status' => true,
            'data' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $shoppingListId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item = Item::create([
            'shopping_list_id' => $shoppingListId,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'quantity' => $validated['quantity'] ?? 1,
            'unit' => $validated['unit'] ?? null,
            'category' => $validated['category'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'added_by' => Auth::id(),
            'is_completed' => false,
        ]);

        $item->load(['addedBy:id,name']);

        return response()->json([
            'status' => true,
            'message' => "Item added successfully",
            'data' => $item,
        ], 201);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::with('shoppingList')->findOrFail($id);
        $shoppingList = $item->shoppingList;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $item->update($validated);

        return response()->json([
            'status' => true,
            'message' => "Item updated successfully",
            'data' => $item->fresh(['addedBy:id,name', 'completedBy:id,name'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return response()->json([
            'status' => true,
            'message' => "Item deleted successfully",
        ]);
    }
}
