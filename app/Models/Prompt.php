<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Prompt
 *
 * Represents an individual AI prompt catalog entry with image previews,
 * categorization, ratings, tags, and tier access restrictions.
 *
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $prompt_text The AI prompt copyable by users
 * @property array|null $images JSON array of image URLs / file paths
 * @property bool $is_premium Restricts full prompt text to Premium members
 * @property bool $is_featured Highlights the prompt on the homepage
 * @property bool $is_upcoming Flags the prompt as an upcoming release preview
 * @property string|null $category Category slug (e.g. 'portrait', 'landscape')
 * @property int $rating Rating from 1 to 5
 * @property string|null $tags Comma-separated tag list
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Prompt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'prompt_text',
        'images',
        'is_premium',
        'is_featured',
        'is_upcoming',
        'category',
        'rating',
        'tags',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_premium' => 'boolean',
        'is_featured' => 'boolean',
        'is_upcoming' => 'boolean',
        'rating' => 'integer',
        'images' => 'array',
    ];

    /**
     * The "booted" method of the model.
     *
     * Automatically ensures backward-compatible schema columns exist on shared hosts.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        self::ensureIsFeaturedColumnExists();
        self::ensureIsUpcomingColumnExists();
    }

    /**
     * Ensure the is_featured column exists in the database table.
     *
     * @return void
     */
    public static function ensureIsFeaturedColumnExists(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('prompts', 'is_featured')) {
                \Illuminate\Support\Facades\Schema::table('prompts', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->boolean('is_featured')->default(false);
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Ensure the is_upcoming column exists in the database table.
     *
     * @return void
     */
    public static function ensureIsUpcomingColumnExists(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('prompts', 'is_upcoming')) {
                \Illuminate\Support\Facades\Schema::table('prompts', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->boolean('is_upcoming')->default(false);
                });
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Get the primary preview / cover image URL for this prompt.
     *
     * @return string|null Image URL or null if no images uploaded.
     */
    public function getFirstImageUrl(): ?string
    {
        if (!empty($this->images) && is_array($this->images)) {
            return $this->images[0] ?? null;
        }
        return null;
    }

    /**
     * Retrieve the list of available categories from database or static fallback.
     *
     * @return array<string, array{label: string, icon: string, color: string}>
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
            // Fallback to static definitions if table is empty or unmigrated
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
     * Get UI display metadata (label, icon emoji, color) for this prompt's category.
     *
     * @return array{label: string, icon: string, color: string}
     */
    public function getCategoryInfo(): array
    {
        $cats = self::categories();
        return $cats[$this->category] ?? ($cats['general'] ?? ['label' => ucfirst($this->category ?? 'General'), 'icon' => '🎨', 'color' => 'purple']);
    }

    /**
     * Get tags formatted as a cleaned array (without '#' prefix).
     *
     * @return array<int, string>
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
     * Get all unique tags across all prompts merged with database tags,
     * including their prompt usage count.
     *
     * @return array<string, int> Associative array of [tagName => count]
     */
    public static function allTags(): array
    {
        $tags = [];
        // From database tags table
        try {
            foreach (\App\Models\Tag::all() as $t) {
                $cleanName = ltrim(trim($t->name), '#');
                if ($cleanName !== '') {
                    $tags[$cleanName] = 0;
                }
            }
        } catch (\Throwable $e) {}

        // From prompts table usage counts
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
