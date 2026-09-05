<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class CategoryController
 *
 * Provides administrative management of prompt categories, unique slug generation,
 * and badge icon / color assignment.
 *
 * @package App\Http\Controllers\Admin
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of categories with prompt counts.
     *
     * @return View
     */
    public function index(): View
    {
        $categories = Category::latest()->paginate(15);
        $totalCategories = Category::count();
        return view('admin.categories.index', compact('categories', 'totalCategories'));
    }

    /**
     * Store a newly created category with auto-generated unique slug.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'icon'  => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
        ]);

        $slug = Str::slug($request->name);
        // Ensure unique slug
        $count = Category::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        Category::create([
            'name'  => $request->name,
            'slug'  => $slug,
            'icon'  => $request->icon ?: '🎨',
            'color' => $request->color ?: 'purple',
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori baru berjaya ditambah!');
    }

    /**
     * Update the specified category attributes.
     *
     * @param  Request   $request
     * @param  Category  $category
     * @return RedirectResponse
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'icon'  => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
        ]);

        $category->update([
            'name'  => $request->name,
            'icon'  => $request->icon ?: '🎨',
            'color' => $request->color ?: 'purple',
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berjaya dikemaskini!');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  Category  $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berjaya dipadam!');
    }
}
