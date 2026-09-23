<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'category_hierarchy', 'child_id', 'parent_id')->orderBy('name');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'category_hierarchy', 'parent_id', 'child_id')->orderBy('name');
    }
}
