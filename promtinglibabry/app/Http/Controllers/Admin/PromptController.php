<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PromptController extends Controller
{
    public function index()
    {
        $prompts = Prompt::latest()->paginate(10);
        $totalPrompts = Prompt::count();
        $freePrompts = Prompt::where('is_premium', false)->count();
        $premiumPrompts = Prompt::where('is_premium', true)->count();
        return view('admin.prompts.index', compact('prompts', 'totalPrompts', 'freePrompts', 'premiumPrompts'));
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
            'images.*' => 'nullable|image|max:2048',
            'is_premium' => 'boolean',
            'category' => 'required|string|in:' . implode(',', array_keys(Prompt::categories())),
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|string|max:500',
        ]);

        $data = $request->except('images');
        $data['is_premium'] = $request->has('is_premium');

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
            'images.*' => 'nullable|image|max:2048',
            'removed_images' => 'nullable|array',
            'removed_images.*' => 'string',
            'is_premium' => 'boolean',
            'category' => 'required|string|in:' . implode(',', array_keys(Prompt::categories())),
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['images', 'removed_images']);
        $data['is_premium'] = $request->has('is_premium');

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
