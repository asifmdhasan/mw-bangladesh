<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function login(): View { return view('admin.login'); }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (Auth::guard('admin')->attempt($credentials + ['role' => 'admin'], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }
        return back()->withErrors(['email' => 'These credentials do not match an admin account.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function dashboard(): View
    {
        $stories = Article::with('category')->latest();
        if (request()->filled('q')) {
            $term = request()->string('q')->toString();
            $stories->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('slug', 'like', "%{$term}%"));
        }
        if (request('status') === 'published') $stories->whereNotNull('published_at');
        if (request('status') === 'draft') $stories->whereNull('published_at');
        if (request()->filled('category_id')) $stories->whereHas('categories', fn ($query) => $query->whereKey(request('category_id')));

        return view('admin.dashboard', ['articles' => $stories->paginate(15)->withQueryString(), 'categories' => $this->categoryTreeItems(), 'articleCount' => Article::count(), 'publishedCount' => Article::whereNotNull('published_at')->count(), 'subscriberCount' => DB::table('newsletter_subscribers')->count(), 'categoryCount' => Category::count(), 'userCount' => User::count()]);
    }

    public function create(): View { return view('admin.create', ['categories' => Category::all(), 'categoryTreeItems' => $this->categoryTreeItems(), 'allCategories' => $this->categoryTreeItems()]); }

    public function edit(Article $article): View { return view('admin.create', ['article' => $article->load(['images', 'categories', 'tags']), 'categories' => Category::all(), 'categoryTreeItems' => $this->categoryTreeItems(), 'allCategories' => $this->categoryTreeItems()]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'], 'category_ids' => ['required', 'array', 'min:1'], 'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
            'excerpt' => ['required', 'string', 'max:320'], 'body' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:500'], 'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'], 'tag_names' => ['nullable', 'string', 'max:2000'], 'is_featured' => ['nullable', 'boolean'], 'publish' => ['nullable', 'boolean'],
            'is_spotlight' => ['nullable', 'boolean'], 'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
        ]);
        $uploadedImages = $request->file('images', []);
        $categoryIds = $data['category_ids'];
        $tagNames = $data['tag_names'] ?? '';
        unset($data['category_ids'], $data['tag_names']);
        $data['category_id'] = $categoryIds[0];
        $data['body'] = $this->sanitizeEditorHtml($data['body']);
        if ($request->hasFile('featured_image')) $data['featured_image'] = $this->saveArticleImage($request->file('featured_image'));
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        $data['author_id'] = Auth::guard('admin')->id();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_spotlight'] = $request->boolean('is_spotlight');
        $data['published_at'] = $request->boolean('publish') ? now() : null;
        unset($data['images']);

        $article = Article::create($data);
        $article->categories()->sync($categoryIds);
        $this->syncTags($article, $tagNames);
        if ($uploadedImages !== []) {
            $directory = public_path('uploads/articles');
            File::ensureDirectoryExists($directory);

            foreach ($uploadedImages as $position => $image) {
                $fileName = Str::uuid().'.'.$image->guessExtension();
                $image->move($directory, $fileName);
                $path = 'uploads/articles/'.$fileName;
                $article->images()->create(['path' => $path, 'alt_text' => $article->title, 'sort_order' => $position]);

                if ($position === 0 && ! $article->image_url) {
                    $article->update(['image_url' => $path]);
                }
            }
        }
        return redirect()->route('admin.dashboard')->with('status', 'Story saved to the magazine.');
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:180'], 'category_ids' => ['required', 'array', 'min:1'], 'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'], 'excerpt' => ['required', 'string', 'max:320'], 'body' => ['required', 'string'], 'image_url' => ['nullable', 'url', 'max:500'], 'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'], 'tag_names' => ['nullable', 'string', 'max:2000'], 'is_featured' => ['nullable', 'boolean'], 'publish' => ['nullable', 'boolean'], 'is_spotlight' => ['nullable', 'boolean'], 'images' => ['nullable', 'array', 'max:12'], 'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240']]);
        $categoryIds = $data['category_ids'];
        $tagNames = $data['tag_names'] ?? '';
        unset($data['category_ids'], $data['tag_names']);
        $data['category_id'] = $categoryIds[0];
        $data['body'] = $this->sanitizeEditorHtml($data['body']);
        if ($request->hasFile('featured_image')) $data['featured_image'] = $this->saveArticleImage($request->file('featured_image'));
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        $data['is_featured'] = $request->boolean('is_featured'); $data['is_spotlight'] = $request->boolean('is_spotlight');
        $data['published_at'] = $request->boolean('publish') ? ($article->published_at ?? now()) : null;
        unset($data['images']); $article->update($data);
        $article->categories()->sync($categoryIds);
        $this->syncTags($article, $tagNames);
        foreach ($request->file('images', []) as $image) {
            $directory = public_path('uploads/articles'); File::ensureDirectoryExists($directory);
            $fileName = Str::uuid().'.'.$image->guessExtension(); $image->move($directory, $fileName);
            $path = 'uploads/articles/'.$fileName;
            $article->images()->create(['path' => $path, 'alt_text' => $article->title, 'sort_order' => $article->images()->count()]);
            if (! $article->image_url) $article->update(['image_url' => $path]);
        }
        return redirect()->route('admin.dashboard')->with('status', 'Story updated.');
    }

    public function destroy(Article $article): RedirectResponse { $article->delete(); return back()->with('status', 'Story deleted.'); }

    public function uploadEditorImage(Request $request)
    {
        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240']]);
        $path = $this->saveArticleImage($request->file('image'));
        return response()->json(['url' => asset($path), 'path' => $path]);
    }

    private function saveArticleImage($image): string
    {
        $directory = public_path('uploads/articles');
        File::ensureDirectoryExists($directory);
        $fileName = Str::uuid().'.'.$image->guessExtension();
        $image->move($directory, $fileName);
        return 'uploads/articles/'.$fileName;
    }

    private function syncTags(Article $article, string $names): void
    {
        $ids = [];
        foreach (array_unique(array_filter(array_map('trim', explode(',', $names)))) as $name) {
            $name = Str::limit($name, 60, '');
            if ($name === '') continue;
            $tag = Tag::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
            if (! $tag) {
                $slug = Str::slug($name) ?: Str::lower(Str::random(8));
                if (Tag::where('slug', $slug)->exists()) $slug .= '-'.Str::lower(Str::random(5));
                $tag = Tag::create(['name' => $name, 'slug' => $slug]);
            }
            $ids[] = $tag->id;
        }
        $article->tags()->sync($ids);
    }

    private function sanitizeEditorHtml(string $html): string
    {
        $allowed = ['p','h1','h2','h3','h4','ul','ol','li','blockquote','strong','b','em','i','u','s','strike','br','hr','a','img','figure','figcaption','table','thead','tbody','tr','th','td','pre','code','span','div'];
        $previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        $xpath = new \DOMXPath($dom);
        foreach (iterator_to_array($xpath->query('//*') ?: []) as $node) {
            if (! $node->parentNode) continue;
            if (! in_array(strtolower($node->nodeName), $allowed, true)) {
                $node->parentNode->removeChild($node);
                continue;
            }
            $safeAttributes = match (strtolower($node->nodeName)) {
                'a' => ['href','target','rel','title','class'],
                'img' => ['src','alt','title','width','height','class','style'],
                'span','p','div','h1','h2','h3','h4','blockquote','td','th' => ['class','style'],
                default => ['class'],
            };
            foreach (iterator_to_array($node->attributes ?? []) as $attribute) {
                $name = strtolower($attribute->name);
                $value = trim($attribute->value);
                if (! in_array($name, $safeAttributes, true) || str_starts_with($name, 'on')) {
                    $node->removeAttributeNode($attribute);
                    continue;
                }
                if (in_array($name, ['href','src'], true) && ! preg_match('/^(?:https?:\/\/|\/|#|mailto:)/i', $value)) $node->removeAttribute($name);
                if ($name === 'style') {
                    $style = $this->sanitizeInlineStyle($value);
                    if ($style === '') $node->removeAttribute('style'); else $node->setAttribute('style', $style);
                }
            }
            if (strtolower($node->nodeName) === 'a' && $node->getAttribute('target') === '_blank') $node->setAttribute('rel', 'noopener noreferrer');
        }
        $wrapper = $dom->getElementsByTagName('div')->item(0);
        $result = '';
        if ($wrapper) foreach ($wrapper->childNodes as $child) $result .= $dom->saveHTML($child);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        return $result;
    }

    private function sanitizeInlineStyle(string $style): string
    {
        $safe = [];
        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) continue;
            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $valid = match ($property) {
                'color','background-color' => preg_match('/^(#[0-9a-f]{3,8}|[a-z]{3,20}|rgba?\([0-9.,% ]+\))$/i', $value),
                'text-align' => in_array(strtolower($value), ['left','right','center','justify'], true),
                'font-size' => preg_match('/^[0-9.]+(?:px|pt|em|rem|%)$/i', $value),
                'font-family' => preg_match('/^[a-z0-9 ,_-]{1,100}$/i', $value),
                'width','height' => preg_match('/^[0-9.]+(?:px|%)$/i', $value),
                default => false,
            };
            if ($valid) $safe[] = $property.': '.$value;
        }
        return implode('; ', $safe);
    }

    public function categories(): View
    {
        $categories = Category::with('parent')->withCount('articles')->get()->keyBy('id');
        $items = $this->categoryTreeItems($categories);
        foreach ($items as &$item) $item['descendant_ids'] = $this->categoryDescendantIds($item['category']->id, $categories);
        unset($item);
        return view('admin.categories', ['categories' => $items, 'allCategories' => $items]);
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $parentId = $request->input('parent_id') ?: null;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('categories', 'name')->where(fn ($query) => $parentId ? $query->where('parent_id', $parentId) : $query->whereNull('parent_id'))->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:240'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id', 'not_in:'.$category->id],
        ]);
        if ($parentId && $this->isCategoryDescendant((int) $parentId, $category->id)) {
            return back()->withErrors(['parent_id' => 'That parent would create a circular category hierarchy.'])->withInput();
        }
        $data['parent_id'] = $parentId;
        $data['slug'] = $this->makeCategorySlug($data['name'], $category->id);
        $category->update($data);
        return back()->with('status', 'Category updated.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->articles()->exists()) return back()->withErrors(['category' => 'Move or delete this category’s stories before removing it.']);
        if ($category->children()->exists()) return back()->withErrors(['category' => 'Move or delete this category’s child categories before removing it.']);
        $category->delete(); return back()->with('status', 'Category deleted.');
    }

    public function users(): View { return view('admin.users', ['users' => User::latest()->paginate(20)]); }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'string', 'min:8'], 'role' => ['required', 'in:admin,subscriber']]);
        User::create($data); return back()->with('status', 'Account created.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id], 'role' => ['required', 'in:admin,subscriber'], 'password' => ['nullable', 'string', 'min:8']]);
        if ($user->is(auth('admin')->user()) && $data['role'] !== 'admin') return back()->withErrors(['role' => 'You cannot remove your own admin access.']);
        if (! empty($data['password'])) $data['password'] = Hash::make($data['password']); else unset($data['password']);
        $user->update($data); return back()->with('status', 'Account updated.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        abort_if($user->is(auth('admin')->user()), 403, 'You cannot delete your own account.');
        $user->delete(); return back()->with('status', 'Account deleted.');
    }

    public function newsletter(): View { return view('admin.newsletter', ['subscribers' => DB::table('newsletter_subscribers')->latest()->paginate(30)]); }

    public function settings(): View { return view('admin.settings', ['settings' => DB::table('site_settings')->pluck('value', 'key')]); }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate(['site_title' => ['required', 'string', 'max:120'], 'tagline' => ['nullable', 'string', 'max:240'], 'ga_measurement_id' => ['nullable', 'regex:/^G-[A-Z0-9]+$/i'], 'adsense_publisher_id' => ['nullable', 'regex:/^ca-pub-[0-9]+$/'], 'facebook_url' => ['nullable', 'url', 'max:500'], 'instagram_url' => ['nullable', 'url', 'max:500'], 'youtube_url' => ['nullable', 'url', 'max:500'], 'site_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp', 'max:5120']]);
        $logoPath = null;
        if ($request->hasFile('site_logo')) {
            $oldLogo = DB::table('site_settings')->where('key', 'site_logo')->value('value');
            $image = $request->file('site_logo');
            $fileName = Str::uuid().'.'.$image->guessExtension();
            File::ensureDirectoryExists(public_path('uploads/site'));
            $image->move(public_path('uploads/site'), $fileName);
            $logoPath = 'uploads/site/'.$fileName;
            DB::table('site_settings')->updateOrInsert(['key' => 'site_logo'], ['value' => $logoPath, 'updated_at' => now(), 'created_at' => now()]);
            if ($oldLogo && str_starts_with($oldLogo, 'uploads/site/') && File::exists(public_path($oldLogo))) File::delete(public_path($oldLogo));
        }
        unset($data['site_logo']);
        foreach ($data as $key => $value) DB::table('site_settings')->updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => now(), 'created_at' => now()]);
        return back()->with('status', 'Site settings saved.');
    }

    public function storeCategory(Request $request)
    {
        $parentId = $request->input('parent_id') ?: null;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('categories', 'name')->where(fn ($query) => $parentId ? $query->where('parent_id', $parentId) : $query->whereNull('parent_id'))],
            'description' => ['nullable', 'string', 'max:240'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $slug = $this->makeCategorySlug($data['name']);
        $category = Category::create(['name' => $data['name'], 'description' => $data['description'] ?? null, 'slug' => $slug, 'parent_id' => $parentId]);
        if (! $request->expectsJson()) return redirect()->route('admin.categories.index')->with('status', 'Category added.');
        $items = $this->categoryTreeItems();
        $item = collect($items)->first(fn ($item) => $item['category']->id === $category->id);
        return response()->json(['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug, 'parent_id' => $category->parent_id, 'path' => $item['path'], 'depth' => $item['depth'], 'message' => 'Category added.']);
    }

    private function makeCategorySlug(string $name, ?int $ignoreId = null): string
    {
        return Category::uniqueSlug($name, $ignoreId);
    }

    private function categoryTreeItems($categories = null): array
    {
        $categories ??= Category::with('children')->orderBy('name')->get()->keyBy('id');
        $categories = $categories->sortBy('name')->values();
        $items = [];
        $walk = function (Category $parent, int $depth, string $path, array $ancestors = []) use (&$walk, &$items, $categories): void {
            if (in_array($parent->id, $ancestors, true)) return;
            $path = $path === '' ? $parent->name : $path.' / '.$parent->name;
            $items[] = ['category' => $parent, 'depth' => $depth, 'path' => $path];
            foreach ($categories->where('parent_id', $parent->id)->sortBy('name') as $child) {
                $walk($child, $depth + 1, $path, [...$ancestors, $parent->id]);
            }
        };
        foreach ($categories->whereNull('parent_id')->sortBy('name') as $root) $walk($root, 0, '');
        return $items;
    }

    private function isCategoryDescendant(int $candidateId, int $categoryId): bool
    {
        $frontier = [$categoryId];
        $visited = [];
        while ($frontier !== []) {
            $frontier = array_values(array_diff($frontier, $visited));
            if ($frontier === []) break;
            $visited = array_merge($visited, $frontier);
            $children = Category::whereIn('parent_id', $frontier)->pluck('id')->all();
            if (in_array($candidateId, $children, true)) return true;
            $frontier = $children;
        }
        return false;
    }

    private function categoryDescendantIds(int $categoryId, $categories): array
    {
        $frontier = [$categoryId];
        $descendants = [];
        while ($frontier !== []) {
            $children = $categories->whereIn('parent_id', $frontier)->pluck('id')->all();
            $children = array_values(array_diff($children, $descendants, $frontier));
            $descendants = array_merge($descendants, $children);
            $frontier = $children;
        }
        return $descendants;
    }
}
