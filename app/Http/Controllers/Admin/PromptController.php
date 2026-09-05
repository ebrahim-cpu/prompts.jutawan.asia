<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class PromptController
 *
 * Administrative controller providing complete CRUD operations,
 * multi-image media management, dynamic filtering, instantaneous status toggling,
 * and high-fidelity CSV and PDF catalog exports.
 *
 * @package App\Http\Controllers\Admin
 */
class PromptController extends Controller
{
    /**
     * Display a listing of prompts with configurable pagination and search/filtering.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
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

        // Catalog statistics
        $totalPrompts = Prompt::count();
        $freePrompts = Prompt::where('is_upcoming', false)->where('is_premium', false)->count();
        $premiumPrompts = Prompt::where('is_upcoming', false)->where('is_premium', true)->count();
        $upcomingPrompts = Prompt::where('is_upcoming', true)->count();

        // Database categories list for filter dropdown
        $dbCategories = \App\Models\Category::all();

        // All tags sorted alphabetically A-Z
        $allTags = Prompt::allTags();
        ksort($allTags, SORT_NATURAL | SORT_FLAG_CASE);

        return view('admin.prompts.index', compact(
            'prompts',
            'totalPrompts',
            'freePrompts',
            'premiumPrompts',
            'upcomingPrompts',
            'perPage',
            'allowedPerPage',
            'dbCategories',
            'allTags',
            'selectedTags'
        ));
    }

    /**
     * Export the prompt repository to Excel-compatible CSV stream or PDF print view.
     *
     * @param  Request  $request
     * @return StreamedResponse|View
     */
    public function export(Request $request): StreamedResponse|View
    {
        $scope = $request->input('scope', 'all'); // 'all' or 'filtered'
        $format = strtolower($request->input('format', 'excel')); // 'excel', 'pdf'

        $query = Prompt::query()->orderBy('updated_at', 'desc');

        if ($scope === 'filtered') {
            if ($request->filled('search')) {
                $search = trim($request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('prompt_text', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

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
        }

        $prompts = $query->get();
        $filename = "prompts_export_" . date('Ymd_His');

        if ($format === 'pdf') {
            return view('admin.prompts.export_pdf', compact('prompts', 'scope', 'filename'));
        }

        // UTF-8 CSV Stream for Excel
        return response()->stream(function() use ($prompts) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 Byte Order Mark for Excel

            fputcsv($handle, [
                'ID',
                'Tajuk Prompt',
                'Kategori',
                'Teks Prompt Sebenar',
                'Penerangan',
                'Tag',
                'Rating',
                'Akses',
                'Tarikh Dicipta',
                'Tarikh Dikemaskini'
            ]);

            foreach ($prompts as $p) {
                $catInfo = $p->getCategoryInfo();
                $accessStatus = $p->is_upcoming ? 'Upcoming' : ($p->is_premium ? 'Premium' : 'Free');
                fputcsv($handle, [
                    $p->id,
                    $p->title,
                    $catInfo['label'] ?? $p->category,
                    $p->prompt_text,
                    $p->description ?? '',
                    implode(', ', $p->getTagsArray()),
                    $p->rating ?? 3,
                    $accessStatus,
                    $p->created_at ? $p->created_at->format('Y-m-d H:i:s') : '',
                    $p->updated_at ? $p->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Show the prompt creation form.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.prompts.create');
    }

    /**
     * Store a newly created prompt and process multiple uploaded image files.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prompt_text' => 'required|string',
            'images.*' => 'nullable|image|max:10240',
            'is_premium' => 'boolean',
            'is_featured' => 'boolean',
            'is_upcoming' => 'boolean',
            'category' => 'required|string|in:' . implode(',', array_keys(Prompt::categories())),
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|string|max:500',
        ]);

        $data = $request->except('images');
        $data['is_premium'] = $request->has('is_premium');
        $data['is_featured'] = $request->has('is_featured');
        $data['is_upcoming'] = $request->has('is_upcoming');

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

    /**
     * Display a specific prompt in the admin dashboard.
     *
     * @param  Prompt  $prompt
     * @return View
     */
    public function show(Prompt $prompt): View
    {
        return view('admin.prompts.show', compact('prompt'));
    }

    /**
     * Show the edit form for modifying an existing prompt.
     *
     * @param  Prompt  $prompt
     * @return View
     */
    public function edit(Prompt $prompt): View
    {
        return view('admin.prompts.edit', compact('prompt'));
    }

    /**
     * Update an existing prompt, handle removed media files, and store new images.
     *
     * @param  Request  $request
     * @param  Prompt   $prompt
     * @return RedirectResponse
     */
    public function update(Request $request, Prompt $prompt): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prompt_text' => 'required|string',
            'images.*' => 'nullable|image|max:10240',
            'removed_images' => 'nullable|array',
            'removed_images.*' => 'string',
            'is_premium' => 'boolean',
            'is_featured' => 'boolean',
            'is_upcoming' => 'boolean',
            'category' => 'required|string|in:' . implode(',', array_keys(Prompt::categories())),
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['images', 'removed_images']);
        $data['is_premium'] = $request->has('is_premium');
        $data['is_featured'] = $request->has('is_featured');
        $data['is_upcoming'] = $request->has('is_upcoming');

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

        // Handle deletions of specific previously uploaded images
        if ($request->has('removed_images')) {
            foreach ($request->removed_images as $toRemove) {
                if (($key = array_search($toRemove, $currentImages)) !== false) {
                    unset($currentImages[$key]);
                    // Delete physically from disk
                    $physicalPath = public_path(ltrim($toRemove, '/'));
                    if (File::exists($physicalPath)) {
                        File::delete($physicalPath);
                    }
                }
            }
            $currentImages = array_values($currentImages);
        }

        // Handle newly uploaded images
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

    /**
     * Delete a prompt from the database and remove all its uploaded images from disk.
     *
     * @param  Prompt  $prompt
     * @return RedirectResponse
     */
    public function destroy(Prompt $prompt): RedirectResponse
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

    /**
     * Instantly toggle is_premium, is_featured, or is_upcoming flags via AJAX.
     *
     * @param  Request  $request
     * @param  Prompt   $prompt
     * @return JsonResponse|RedirectResponse
     */
    public function toggleStatus(Request $request, Prompt $prompt): JsonResponse|RedirectResponse
    {
        if ($request->has('is_premium')) {
            $prompt->is_premium = (bool)$request->input('is_premium');
        }
        if ($request->has('is_featured')) {
            $prompt->is_featured = (bool)$request->input('is_featured');
        }
        if ($request->has('is_upcoming')) {
            $prompt->is_upcoming = (bool)$request->input('is_upcoming');
        }
        $prompt->save();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status prompt berjaya dikemaskini!',
                'is_premium' => (bool)$prompt->is_premium,
                'is_featured' => (bool)$prompt->is_featured,
                'is_upcoming' => (bool)$prompt->is_upcoming,
            ]);
        }

        return redirect()->back()->with('success', 'Status prompt berjaya dikemaskini!');
    }
}
