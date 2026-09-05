<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class HomeController
 *
 * Serves the public storefront / landing catalog of featured prompts,
 * prompt detail view, dynamic filtering (category, rating, tags, tier),
 * and live visitor counters.
 *
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Display the main landing page with featured prompts catalog,
     * searchable filters, category counters, and visitor statistics.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Prompt::query()->where('is_featured', true);

        // Fulltext search across title, description, prompt text, and tags
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('prompt_text', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Filter by tier: free, premium, or upcoming
        if ($request->filled('filter') && $request->filter !== 'all') {
            if ($request->filter === 'free') {
                $query->where('is_upcoming', false)->where('is_premium', false);
            } elseif ($request->filter === 'premium') {
                $query->where('is_upcoming', false)->where('is_premium', true);
            } elseif ($request->filter === 'upcoming') {
                $query->where('is_upcoming', true);
            }
        }

        // Filter by category slug
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by minimum rating (1-5)
        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', (int) $request->rating);
        }

        // Filter by specific tag keyword
        if ($request->filled('tag')) {
            $tag = $request->tag;
            $query->where('tags', 'like', "%{$tag}%");
        }

        $prompts = $query->latest()->paginate(12)->withQueryString();

        // Aggregated prompt counts for featured collection
        $totalPrompts = Prompt::where('is_featured', true)->count();
        $freePrompts = Prompt::where('is_featured', true)->where('is_upcoming', false)->where('is_premium', false)->count();
        $premiumPrompts = Prompt::where('is_featured', true)->where('is_upcoming', false)->where('is_premium', true)->count();
        $upcomingPrompts = Prompt::where('is_featured', true)->where('is_upcoming', true)->count();

        // Traffic metrics for display badge
        $totalVisitors = VisitorLog::count();
        $uniqueVisitors = VisitorLog::distinct('ip_address')->count('ip_address');

        // Dynamic categories with prompt count breakdown
        $categories = Prompt::categories();
        $categoryCounts = Prompt::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Alphabetically sorted tags list
        $allTags = Prompt::allTags();

        return view('welcome', compact(
            'prompts',
            'totalPrompts',
            'freePrompts',
            'premiumPrompts',
            'upcomingPrompts',
            'totalVisitors',
            'uniqueVisitors',
            'categories',
            'categoryCounts',
            'allTags'
        ));
    }

    /**
     * Display the detail page for a single prompt, along with
     * intelligently computed related recommendations.
     *
     * @param  Prompt  $prompt
     * @return View
     */
    public function show(Prompt $prompt): View
    {
        // Extract sanitized tags
        $tags = $prompt->getTagsArray();

        // Fetch related prompts matching category or overlapping tags
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

        // Backfill with newest featured prompts if recommendations are fewer than 4
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
