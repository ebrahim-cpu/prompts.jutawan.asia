<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $fillable = [
        'title',
        'description',
        'prompt_text',
        'images',
        'is_premium',
        'category',
        'rating',
        'tags',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'rating' => 'integer',
        'images' => 'array',
    ];

    /**
     * Get the first image url to display as a cover.
     */
    public function getFirstImageUrl(): ?string
    {
        if (!empty($this->images) && is_array($this->images)) {
            return $this->images[0] ?? null;
        }
        return null;
    }

    /**
     * Available categories for prompts (fetched dynamically from database).
     */
    public static function categories(): array
    {
        try {
            $dbCats = \App\Models\Category::orderBy('name', 'asc')->get();
            if ($dbCats->count() > 0) {
                $cats = [];
                foreach ($dbCats as $c) {
                    $cats[$c->slug] = [
                        'label' => $c->name,
                        'icon'  => $c->icon ?: '🎨',
                        'color' => $c->color ?: 'purple',
                    ];
                }
                return $cats;
            }
        } catch (\Throwable $e) {
            // Fallback if table not ready
        }

        $fallback = [
            'general'      => ['label' => 'Umum',          'icon' => '🎨', 'color' => 'gray'],
            'portrait'     => ['label' => 'Potret',        'icon' => '🧑', 'color' => 'pink'],
            'landscape'    => ['label' => 'Landskap',      'icon' => '🏔️', 'color' => 'green'],
            'anime'        => ['label' => 'Anime',         'icon' => '⛩️', 'color' => 'purple'],
            'realistic'    => ['label' => 'Realistik',     'icon' => '📷', 'color' => 'blue'],
            'abstract'     => ['label' => 'Abstrak',       'icon' => '🌀', 'color' => 'indigo'],
            'fantasy'      => ['label' => 'Fantasi',       'icon' => '🐉', 'color' => 'yellow'],
            'scifi'        => ['label' => 'Sci-Fi',        'icon' => '🚀', 'color' => 'cyan'],
            'architecture' => ['label' => 'Arkitektur',  'icon' => '🏛️', 'color' => 'amber'],
            'food'         => ['label' => 'Makanan',       'icon' => '🍜', 'color' => 'orange'],
            'nature'       => ['label' => 'Alam Semula Jadi', 'icon' => '🌿', 'color' => 'emerald'],
            'logo'         => ['label' => 'Logo & Ikon',   'icon' => '✏️', 'color' => 'rose'],
        ];

        uasort($fallback, function($a, $b) {
            return strnatcasecmp($a['label'], $b['label']);
        });

        return $fallback;
    }

    /**
     * Get the category details.
     */
    public function getCategoryInfo(): array
    {
        $cats = self::categories();
        return $cats[$this->category] ?? ($cats['general'] ?? ['label' => ucfirst($this->category ?? 'General'), 'icon' => '🎨', 'color' => 'purple']);
    }

    /**
     * Get tags as array.
     */
    public function getTagsArray(): array
    {
        if (empty($this->tags)) return [];
        $raw = array_filter(array_map('trim', explode(',', $this->tags)));
        return array_values(array_unique(array_filter(array_map(function ($tag) {
            return ltrim(trim($tag), '#');
        }, $raw))));
    }

    /**
     * Get all unique tags across prompts and Tag table.
     */
    public static function allTags(): array
    {
        $tags = [];
        // From db tags table
        try {
            foreach (\App\Models\Tag::all() as $t) {
                $cleanName = ltrim(trim($t->name), '#');
                if ($cleanName !== '') {
                    $tags[$cleanName] = 0;
                }
            }
        } catch (\Throwable $e) {}

        // From prompts table usage
        foreach (self::whereNotNull('tags')->where('tags', '!=', '')->pluck('tags') as $tagString) {
            foreach (explode(',', $tagString) as $tag) {
                $cleanTag = ltrim(trim($tag), '#');
                if ($cleanTag !== '') {
                    $tags[$cleanTag] = ($tags[$cleanTag] ?? 0) + 1;
                }
            }
        }
        ksort($tags, SORT_NATURAL | SORT_FLAG_CASE);
        return $tags;
    }
}
