<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of hashtags.
     */
    public function index()
    {
        $tags = Tag::latest()->paginate(20);
        $totalTags = Tag::count();
        return view('admin.tags.index', compact('tags', 'totalTags'));
    }

    /**
     * Store a newly created hashtag.
     */
    public function store(Request $request)
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

        return redirect()->route('admin.tags.index')->with('success', 'Hashtag baru berjaya ditambah!');
    }

    /**
     * Update the specified hashtag.
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = trim($request->name, '# ');

        $tag->update([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Hashtag berjaya dikemaskini!');
    }

    /**
     * Remove the specified hashtag.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('admin.tags.index')->with('success', 'Hashtag berjaya dipadam!');
    }
}
