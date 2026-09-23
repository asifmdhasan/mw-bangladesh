<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('magazine.index', ['articles' => $articles->paginate(9)->withQueryString(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function category(Category $category): View
    {
        return view('magazine.index', ['category' => $category, 'articles' => $category->articles()->with(['category', 'images'])->whereNotNull('published_at')->latest('published_at')->paginate(9), 'categories' => Category::orderBy('name')->get()]);
    }

    public function show(Article $article): View
    {
        abort_unless($article->published_at, 404);
        $article->load('category', 'author', 'images');
        $galleryImages = $article->images;
        if ($galleryImages->first()?->path === $article->image_url) {
            $galleryImages = $galleryImages->skip(1);
        }

        return view('magazine.show', [
            'article' => $article,
            'galleryImages' => $galleryImages,
            'related' => Article::with(['category', 'images'])->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $article->categories()->select('categories.id')))->whereKeyNot($article->id)->whereNotNull('published_at')->latest('published_at')->take(3)->get(),
        ]);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:255'],
        ]);
        DB::table('newsletter_subscribers')->updateOrInsert(['email' => $validated['email']], [
            'name' => $validated['name'] ?? null, 'phone' => $validated['phone'] ?? null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return back()->with('newsletter_success', 'You are on the list. See you in your inbox.');
    }
}
