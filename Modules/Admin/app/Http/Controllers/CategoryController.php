<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin::categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin::categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon_class' => 'nullable|string|max:100',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon_class' => $request->icon_class,
            'is_active' => true,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category catalog updated.');
    }

    public function edit(Category $category)
    {
        return view('admin::categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'icon_class' => 'nullable|string|max:100',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon_class' => $request->icon_class,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category intelligence synchronized.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category decommissioned.');
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return response()->json(['success' => true, 'status' => $category->is_active]);
    }
}
