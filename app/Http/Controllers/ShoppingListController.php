<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // GET all list of shopping lists
    public function index()
    {
        $lists = ShoppingList::where('created_by', Auth::id())
        ->with('creator:id,name,email')
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'data' => $lists,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    // GET to a page to create a new shopping list
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    // POST/shopping-lists
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_shared' => 'boolean',
        ]);

        $shoppingList = ShoppingList::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
            'status' => 'active',
            'is_shared' => $validated['is_shared'] ?? false,
        ]);

        // load creator relationship
        $shoppingList->load('creator:id,name,email');

        return response()->json([
            'status' => true,
            'message' => 'Shopping list created successfully.',
            'data' => $shoppingList
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    // GET specified page of a shopping list page 
    public function show(string $id)
    {
        $shoppingList = ShoppingList::with('creator:id,name,email')
        ->where('created_by', Auth::id())
        ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $shoppingList,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // GET specified page to update a shopping list details
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shoppingList = ShoppingList::where('created_by', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_shared' => 'boolean',
        ]);

        $shoppingList->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Shopping list updated successfully.',
            'data' => $shoppingList,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */

    // DELETE/shopping-lists
    public function destroy(string $id)
    {

        // Later add function for is_shared = true / only delete if family creator @ list admin
        $shoppingList = ShoppingList::where('created_by', Auth::id())->findOrFail($id);
        $shoppingList->delete();

        return response()->json([
            'status' => true,
            'message' => 'Shopping list deleted successfully'
        ]);
    }
}
