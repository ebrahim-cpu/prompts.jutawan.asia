<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class TagController
 *
 * Administrative controller for managing taxonomy tags used across prompt records.
 *
 * @package App\Http\Controllers\Admin
 */
class TagController extends Controller
{
    /**
     * Display a listing of tags sorted alphabetically with prompt usage counts.
     *
     * @return View
     */
    public function index(): View
    {
        $tags = Tag::orderBy('name', 'asc')->paginate(50);
        $totalTags = Tag::count();
        return view('admin.tags.index', compact('tags', 'totalTags'));
    }

    /**
     * Store a newly created tag, stripping any leading '#' symbols.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = trim($request->name, '# ');
        $slug = Str::slug($name);

        $count = Tag::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug = "{$slug}-" . ($count + 1);
        }

        Tag::create([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag baru berjaya ditambah!');
    }

    /**
     * Update the specified tag.
     *
     * @param  Request  $request
     * @param  Tag      $tag
     * @return RedirectResponse
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = trim($request->name, '# ');

        $tag->update([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag berjaya dikemaskini!');
    }

    /**
     * Remove the specified tag from database.
     *
     * @param  Tag  $tag
     * @return RedirectResponse
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();
        return redirect()->route('admin.tags.index')->with('success', 'Tag berjaya dipadam!');
    }
}
