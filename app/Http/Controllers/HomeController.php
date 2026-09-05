<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Prompt::query()->where('is_featured', true);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('prompt_text', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Filter by tier
        if ($request->filled('filter') && $request->filter !== 'all') {
            if ($request->filter === 'free') {
                $query->where('is_upcoming', false)->where('is_premium', false);
            } elseif ($request->filter === 'premium') {
                $query->where('is_upcoming', false)->where('is_premium', true);
            } elseif ($request->filter === 'upcoming') {
                $query->where('is_upcoming', true);
            }
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by rating
        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', (int) $request->rating);
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $tag = $request->tag;
            $query->where('tags', 'like', "%{$tag}%");
        }

        $prompts = $query->latest()->paginate(12)->withQueryString();

        // Stats (For Featured prompts on Home Page)
        $totalPrompts = Prompt::where('is_featured', true)->count();
        $freePrompts = Prompt::where('is_featured', true)->where('is_upcoming', false)->where('is_premium', false)->count();
        $premiumPrompts = Prompt::where('is_featured', true)->where('is_upcoming', false)->where('is_premium', true)->count();
        $upcomingPrompts = Prompt::where('is_featured', true)->where('is_upcoming', true)->count();

        // Visitor Counter Stats
        $totalVisitors = \App\Models\VisitorLog::count();
        $uniqueVisitors = \App\Models\VisitorLog::distinct('ip_address')->count('ip_address');

        // Categories with counts
        $categories = Prompt::categories();
        $categoryCounts = Prompt::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // All tags
        $allTags = Prompt::allTags();

        return view('welcome', compact('prompts', 'totalPrompts', 'freePrompts', 'premiumPrompts', 'upcomingPrompts', 'totalVisitors', 'uniqueVisitors', 'categories', 'categoryCounts', 'allTags'));
    }

    public function show(Prompt $prompt)
    {
        // Extract tags array from prompt
        $tags = $prompt->getTagsArray();

        // Fetch related prompts matching category OR tags
        $relatedQuery = Prompt::query()
            ->where('id', '!=', $prompt->id)
            ->where(function ($q) use ($prompt, $tags) {
                $q->where('category', $prompt->category);
                if (!empty($tags)) {
                    foreach ($tags as $t) {
                        $q->orWhere('tags', 'like', "%{$t}%");
                    }
                }
            });

        $relatedPrompts = $relatedQuery->latest()->take(8)->get();

        // If not enough related prompts by category/tags, fill with recent featured prompts
        if ($relatedPrompts->count() < 4) {
            $existingIds = $relatedPrompts->pluck('id')->push($prompt->id)->toArray();
            $additional = Prompt::whereNotIn('id', $existingIds)
                ->where('is_featured', true)
                ->latest()
                ->take(8 - $relatedPrompts->count())
                ->get();
            $relatedPrompts = $relatedPrompts->merge($additional);
        }

        $categories = Prompt::categories();

        return view('prompts.show', compact('prompt', 'relatedPrompts', 'categories'));
    }
}
