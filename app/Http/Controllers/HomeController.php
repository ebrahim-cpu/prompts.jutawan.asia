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
                $query->where('is_premium', false);
            } elseif ($request->filter === 'premium') {
                $query->where('is_premium', true);
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
        $freePrompts = Prompt::where('is_featured', true)->where('is_premium', false)->count();
        $premiumPrompts = Prompt::where('is_featured', true)->where('is_premium', true)->count();

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

        return view('welcome', compact('prompts', 'totalPrompts', 'freePrompts', 'premiumPrompts', 'totalVisitors', 'uniqueVisitors', 'categories', 'categoryCounts', 'allTags'));
    }
}
