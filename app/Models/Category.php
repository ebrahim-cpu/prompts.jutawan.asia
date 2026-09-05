<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Category
 *
 * Represents an administrative category for grouping AI prompts.
 *
 * @package App\Models
 * @property int $id
 * @property string $name Display name (e.g., "Portrait", "Anime")
 * @property string $slug URL-friendly unique identifier
 * @property string|null $icon Emoji or icon symbol
 * @property string|null $color Tailwind color theme
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int $prompts_count Number of prompts assigned to this category
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
    ];

    /**
     * Compute total count of prompts categorized under this category's slug.
     *
     * @return int
     */
    public function getPromptsCountAttribute(): int
    {
        return Prompt::where('category', $this->slug)->count();
    }
}
