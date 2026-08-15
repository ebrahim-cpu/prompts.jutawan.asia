<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get usage count across prompts.
     */
    public function getUsageCountAttribute(): int
    {
        return Prompt::where('tags', 'like', "%{$this->name}%")->count();
    }
}
