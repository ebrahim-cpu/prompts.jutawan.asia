<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PromptController extends Controller
{
    public function index(Request $request)
    {
        $allowedPerPage = [50, 100, 150, 200, 300];
        $perPage = (int) $request->input('per_page', 50);

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 50;
        }

        $query = Prompt::query()->orderBy('updated_at', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('prompt_text', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Tag Filter (Multiple Checkboxes Support)
        $selectedTags = [];
        if ($request->has('tags')) {
            $rawTags = $request->input('tags');
            if (is_array($rawTags)) {
                $selectedTags = array_values(array_unique(array_filter(array_map(fn($t) => ltrim(trim($t), '#'), $rawTags))));
            } elseif (is_string($rawTags) && $rawTags !== 'all' && trim($rawTags) !== '') {
                $selectedTags = array_values(array_unique(array_filter(array_map(fn($t) => ltrim(trim($t), '#'), explode(',', $rawTags)))));
            }
        }

        if (!empty($selectedTags)) {
            $query->where(function ($q) use ($selectedTags) {
                foreach ($selectedTags as $t) {
                    $q->orWhere('tags', 'like', "%{$t}%");
                }
            });
        }

        $prompts = $query->paginate($perPage)->withQueryString();

        // Stats
        $totalPrompts = Prompt::count();
        $freePrompts = Prompt::where('is_premium', false)->count();
        $premiumPrompts = Prompt::where('is_premium', true)->count();

        // Categories list for filter
        $dbCategories = \App\Models\Category::all();

        // All tags list for filter (sorted alphabetically ascending A-Z)
        $allTags = Prompt::allTags();
        ksort($allTags, SORT_NATURAL | SORT_FLAG_CASE);

        return view('admin.prompts.index', compact(
            'prompts',
            'totalPrompts',
            'freePrompts',
            'premiumPrompts',
            'perPage',
            'allowedPerPage',
            'dbCategories',
            'allTags',
            'selectedTags'
        ));
    }

    public function create()
    {
        return view('admin.prompts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prompt_text' => 'required|string',
            'images.*' => 'nullable|image|max:10240',
            'is_premium' => 'boolean',
            'category' => 'required|string|in:' . implode(',', array_keys(Prompt::categories())),
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|string|max:500',
        ]);

        $data = $request->except('images');
        $data['is_premium'] = $request->has('is_premium');

        if (!empty($request->tags)) {
            $raw = explode(',', $request->tags);
            $clean = array_values(array_unique(array_filter(array_map(function($t) {
                return ltrim(trim($t), '#');
            }, $raw))));
            $data['tags'] = implode(', ', $clean);
        } else {
            $data['tags'] = null;
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            $uploadPath = public_path('uploads/prompts');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $imagePaths[] = '/uploads/prompts/' . $filename;
            }
        }
        $data['images'] = $imagePaths;

        Prompt::create($data);

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt berjaya ditambah!');
    }

    public function show(Prompt $prompt)
    {
        return view('admin.prompts.show', compact('prompt'));
    }

    public function edit(Prompt $prompt)
    {
        return view('admin.prompts.edit', compact('prompt'));
    }

    public function update(Request $request, Prompt $prompt)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prompt_text' => 'required|string',
            'images.*' => 'nullable|image|max:10240',
            'removed_images' => 'nullable|array',
            'removed_images.*' => 'string',
            'is_premium' => 'boolean',
            'category' => 'required|string|in:' . implode(',', array_keys(Prompt::categories())),
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['images', 'removed_images']);
        $data['is_premium'] = $request->has('is_premium');

        if (!empty($request->tags)) {
            $raw = explode(',', $request->tags);
            $clean = array_values(array_unique(array_filter(array_map(function($t) {
                return ltrim(trim($t), '#');
            }, $raw))));
            $data['tags'] = implode(', ', $clean);
        } else {
            $data['tags'] = null;
        }

        $currentImages = $prompt->images ?? [];

        // Handle deletions
        if ($request->has('removed_images')) {
            foreach ($request->removed_images as $toRemove) {
                if (($key = array_search($toRemove, $currentImages)) !== false) {
                    unset($currentImages[$key]);
                    // Delete physically
                    $physicalPath = public_path(ltrim($toRemove, '/'));
                    if (File::exists($physicalPath)) {
                        File::delete($physicalPath);
                    }
                }
            }
            $currentImages = array_values($currentImages); // reindex
        }

        // Handle new uploads
        if ($request->hasFile('images')) {
            $uploadPath = public_path('uploads/prompts');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $currentImages[] = '/uploads/prompts/' . $filename;
            }
        }

        $data['images'] = $currentImages;
        $prompt->update($data);

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt berjaya dikemaskini!');
    }

    public function destroy(Prompt $prompt)
    {
        if (!empty($prompt->images)) {
            foreach ($prompt->images as $img) {
                $physicalPath = public_path(ltrim($img, '/'));
                if (File::exists($physicalPath)) {
                    File::delete($physicalPath);
                }
            }
        }
        $prompt->delete();
        
        return redirect()->route('admin.prompts.index')->with('success', 'Prompt berjaya dipadam!');
    }
}
