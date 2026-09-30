<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'parent_id'];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        $parent = $this->parent;
        $visited = [$this->id];

        while ($parent && ! in_array($parent->id, $visited, true)) {
            $name = $parent->name.' / '.$name;
            $visited[] = $parent->id;
            $parent = $parent->parent;
        }

        return $name;
    }

    public function getSlugPathAttribute(): string
    {
        $parts = [$this->slug];
        $parent = $this->parent;
        $visited = [$this->id];

        while ($parent && ! in_array($parent->id, $visited, true)) {
            array_unshift($parts, $parent->slug);
            $visited[] = $parent->id;
            $parent = $parent->parent;
        }

        return implode('/', $parts);
    }

    public function getUrlAttribute(): string
    {
        return url('/'.$this->slug_path);
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $suffix = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
