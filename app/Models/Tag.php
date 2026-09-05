<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Tag
 *
 * Represents an individual metadata tag for categorizing and searching prompts.
 *
 * @package App\Models
 * @property int $id
 * @property string $name Clean tag title without leading '#'
 * @property string $slug URL-friendly identifier
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int $usage_count Total number of prompts referencing this tag
 */
class Tag extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Calculate prompt usage count across all prompt tag strings.
     *
     * @return int
     */
    public function getUsageCountAttribute(): int
    {
        return Prompt::where('tags', 'like', "%{$this->name}%")->count();
    }
}
