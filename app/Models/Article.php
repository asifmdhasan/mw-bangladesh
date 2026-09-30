<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = ['category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'image_url', 'featured_image', 'mobile_featured_image', 'is_featured', 'is_spotlight', 'published_at'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_spotlight' => 'boolean', 'published_at' => 'datetime'];
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function categories(): BelongsToMany { return $this->belongsToMany(Category::class)->orderBy('name'); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class)->orderBy('name'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function images(): HasMany { return $this->hasMany(ArticleImage::class)->orderBy('sort_order'); }

    public function getImageSrcAttribute(): string
    {
        $path = $this->featured_image ?: $this->image_url ?: $this->images->first()?->path;

        if (! $path) {
            return 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=1400&q=85';
        }

        return Str::startsWith($path, ['https://', 'http://']) ? $path : asset($path);
    }

    public function getMobileImageSrcAttribute(): string
    {
        $path = $this->mobile_featured_image;

        return $path
            ? (Str::startsWith($path, ['https://', 'http://']) ? $path : asset($path))
            : $this->image_src;
    }
}
