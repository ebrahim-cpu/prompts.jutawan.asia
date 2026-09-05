<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
    ];

    /**
     * Get prompt count in this category.
     */
    public function getPromptsCountAttribute(): int
    {
        return Prompt::where('category', $this->slug)->count();
    }
}
