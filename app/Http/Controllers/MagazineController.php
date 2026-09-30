<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterWelcome;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class MagazineController extends Controller
{
    public function home(): View
    {
        $spotlight = Article::with(['category', 'images'])->whereNotNull('published_at')->where('is_spotlight', true)->latest('published_at')->take(7)->get();
        if ($spotlight->count() < 7) {
            $spotlight = $spotlight->concat(Article::with(['category', 'images'])->whereNotNull('published_at')
                ->when($spotlight->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $spotlight->modelKeys()))
                ->latest('published_at')->take(7 - $spotlight->count())->get());
        }

        $latest = Article::with(['category', 'images'])->whereNotNull('published_at')->latest('published_at')->take(7)->get();
        $categories = Category::whereIn('slug', ['style', 'entertainment', 'magazine'])->get()->keyBy('slug');
        $sections = collect(['style', 'entertainment', 'magazine'])->map(function (string $slug) use ($categories) {
            $category = $categories->get($slug);
            return [
                'title' => $category?->name ?? ucfirst($slug),
                'category' => $category,
                'articles' => $category
                    ? $category->articles()->with(['category', 'images'])->whereNotNull('published_at')->latest('published_at')->take(7)->get()
                    : collect(),
            ];
        });

        return view('magazine.home', [
            'spotlight' => $spotlight,
            'latest' => $latest,
            'sections' => $sections,
        ]);
    }

    public function index(Request $request): View
    {
        $articles = Article::with(['category', 'images'])->whereNotNull('published_at')->latest('published_at');
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $articles->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%"));
        }
        return view('magazine.index', ['articles' => $articles->paginate(9)->withQueryString(), 'categories' => $this->categoryNavigation()]);
    }

    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        $term = is_string($query) ? trim($query) : '';
        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $articles = Article::with(['category', 'images'])
            ->whereNotNull('published_at')
            ->where(fn ($query) => $query->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%"))
            ->latest('published_at')
            ->take(8)
            ->get();

        return response()->json($articles->map(fn (Article $article) => [
            'title' => $article->title,
            'category' => $article->category?->display_name ?? 'STORY',
            'excerpt' => $article->excerpt,
            'image' => $article->image_src,
            'url' => route('articles.show', $article),
        ])->values());
    }

    public function category(Category $category): View
    {
        $category->loadMissing(['parent.children', 'children']);
        $activeParentCategory = $category->parent ?? $category;
        $categoryIds = collect([$category->id]);

        if ($category->parent_id === null) {
            $categoryIds = $categoryIds->merge($category->children->modelKeys());
        }

        $articles = Article::with(['category', 'images'])
            ->whereNotNull('published_at')
            ->where(function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds)
                    ->orWhereHas('categories', fn ($categories) => $categories->whereIn('categories.id', $categoryIds));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('magazine.index', [
            'category' => $category,
            'activeParentCategory' => $activeParentCategory,
            'articles' => $articles,
            'categories' => $this->categoryNavigation(),
        ]);
    }

    public function categoryPath(string $path): View
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $category = Category::where('slug', end($segments) ?: '')->firstOrFail();
        abort_unless($category->slug_path === implode('/', $segments), 404);

        return $this->category($category);
    }

    public function show(Article $article): View
    {
        abort_unless($article->published_at, 404);
        $article->load('category', 'categories.parent', 'tags', 'author', 'images');
        $galleryImages = $article->images;
        if ($galleryImages->first()?->path === $article->image_url) {
            $galleryImages = $galleryImages->skip(1);
        }

        $categoryIds = $article->categories->modelKeys();
        if ($categoryIds === []) {
            $categoryIds = [$article->category_id];
        }
        $inArticleCategories = fn ($query) => $query->where(function ($query) use ($article, $categoryIds) {
            $query->where('category_id', $article->category_id)
                ->orWhereHas('categories', fn ($categories) => $categories->whereIn('categories.id', $categoryIds));
        });
        $categoryStories = Article::query()->whereNotNull('published_at')->where($inArticleCategories);
        $previous = (clone $categoryStories)->where(function ($query) use ($article) {
            $query->where('published_at', '<', $article->published_at)
                ->orWhere(fn ($query) => $query->where('published_at', $article->published_at)->where('id', '<', $article->id));
        })->latest('published_at')->latest('id')->first();
        $next = (clone $categoryStories)->where(function ($query) use ($article) {
            $query->where('published_at', '>', $article->published_at)
                ->orWhere(fn ($query) => $query->where('published_at', $article->published_at)->where('id', '>', $article->id));
        })->oldest('published_at')->oldest('id')->first();

        return view('magazine.show', [
            'article' => $article,
            'galleryImages' => $galleryImages,
            'related' => (clone $categoryStories)->with(['category', 'images'])->whereKeyNot($article->id)->latest('published_at')->latest('id')->take(4)->get(),
            'previous' => $previous,
            'next' => $next,
        ]);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:255'],
        ]);
        if ($validator->fails()) {
            return back()->withFragment('newsletter')->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $isNewSubscriber = ! DB::table('newsletter_subscribers')->where('email', $validated['email'])->exists();
        DB::table('newsletter_subscribers')->updateOrInsert(['email' => $validated['email']], [
            'name' => $validated['name'] ?? null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        if ($isNewSubscriber) {
            Mail::to($validated['email'])->queue(new NewsletterWelcome($validated['name'] ?? null));
        }

        return back()->withFragment('newsletter')->with('newsletter_success', 'Thanks for subscribing! You are on the list. See you in your inbox.');
    }

    private function categoryNavigation()
    {
        return Category::with('children')->whereNull('parent_id')->orderBy('name')->get();
    }
}
