<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Category::withCount('products');

        if ($request->has('status')) {
            $query->where('is_active', strtolower((string) $request->input('status')) === 'active');
        } elseif ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        } elseif ($request->boolean('active_only') || $request->query('only_active') === '1') {
            $query->where('is_active', true);
        }

        $categories = $query->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        if (isset($input['status']) && ! isset($input['is_active'])) {
            $input['is_active'] = strtolower((string) $input['status']) === 'active';
        }
        $request->merge($input);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'badge' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $count = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$count++;
            }
            $validated['slug'] = $slug;
        }

        if (! isset($validated['display_order'])) {
            $validated['display_order'] = Category::count() + 1;
        }
        if (! isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $category = Category::create($validated);

        AuditLog::record(
            action: 'CREATE',
            entityType: 'Category',
            description: "Created category '{$category->name}' (ID: {$category->id})",
            entityId: (string) $category->id,
            entityName: $category->name,
            changes: $category->toArray(),
            request: $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category->loadCount('products'),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $category = Category::where('id', $id)
            ->orWhere('slug', $id)
            ->with(['products'])
            ->withCount('products')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $category = Category::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        $input = $request->all();
        if (isset($input['status']) && ! isset($input['is_active'])) {
            $input['is_active'] = strtolower((string) $input['status']) === 'active';
        }
        $request->merge($input);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'badge' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|string',
        ]);

        if (isset($validated['name']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        AuditLog::record(
            action: 'UPDATE',
            entityType: 'Category',
            description: "Updated category '{$category->name}'",
            entityId: (string) $category->id,
            entityName: $category->name,
            changes: $validated,
            request: $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category->loadCount('products'),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $category = Category::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        // If requested to delete products or if cascade is set, delete them
        if ($request->boolean('delete_products') || $request->input('cascade') === true) {
            $category->products()->delete();
        } else {
            // Nullify foreign key on products
            $category->products()->update([
                'category_id' => null,
                'category_slug' => 'uncategorized',
            ]);
        }

        $catName = $category->name;
        $catId = (string) $category->id;
        $category->delete();

        AuditLog::record(
            action: 'DELETE',
            entityType: 'Category',
            description: "Deleted category '{$catName}'",
            entityId: $catId,
            entityName: $catName,
            request: $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
