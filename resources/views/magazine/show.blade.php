@extends('layouts.magazine')
@section('title', $article->title.' — MW Bangladesh')
@section('meta')
<meta name="description" content="{{ $article->excerpt }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $article->title }}">
<meta property="og:description" content="{{ $article->excerpt }}">
<meta property="og:image" content="{{ $article->image_src }}">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $article->title }}">
<meta name="twitter:description" content="{{ $article->excerpt }}">
<meta name="twitter:image" content="{{ $article->image_src }}">
@endsection
@section('content')
<main class="article-page">
    <article>
        <div class="container article-cover">
            <picture><source media="(max-width: 767.98px)" srcset="{{ $article->mobile_image_src }}"><img src="{{ $article->image_src }}" alt="{{ $article->images->first()?->alt_text ?? $article->title }}"></picture>
        </div>

        <div class="container article-content-layout">
            <aside class="article-share" aria-label="Share this story">
                <span>SHARE</span>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on X">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.9 2H22l-6.8 7.8L23.2 22h-6.3L12 14.6 5.5 22H2.3l7.3-8.4L1.8 2h6.5l4.5 6.8L18.9 2Zm-1.1 18h1.7L7.3 3.9H5.5L17.8 20Z"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.5 22v-9h3l.5-3.5h-3.5V7.3c0-1 .3-1.8 1.8-1.8h1.9V2.4c-.9-.1-1.9-.2-2.9-.2-2.9 0-4.8 1.8-4.8 5v2.3H6.3V13h3.2v9h4Z"/></svg>
                </a>
                <a href="https://wa.me/?text={{ urlencode($article->title.' '.request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 2 17.7L.5 23.5l6-1.6a11.8 11.8 0 0 0 14-18.4ZM12 21a9.8 9.8 0 0 1-5-1.4l-.4-.2-3.5.9.9-3.4-.2-.4A9.8 9.8 0 1 1 12 21Zm5.4-7.3c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2l-.9 1.1c-.2.2-.3.2-.6.1a8 8 0 0 1-2.4-1.5 9 9 0 0 1-1.7-2.1c-.2-.3 0-.4.1-.6l.5-.6.3-.5c.1-.2 0-.4 0-.5l-.9-2.1c-.2-.5-.5-.4-.7-.4h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.3s1 2.6 1.1 2.8c.1.2 2 3 4.7 4.2 1.7.7 2.4.8 3.2.7.5-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.1-1.4-.1-.1-.3-.2-.6-.3Z"/></svg>
                </a>
            </aside>

            <div class="article-main-content">
                <header class="article-heading">
                    <div class="article-categories">
                        @forelse($article->categories as $category)
                            <a class="eyebrow" href="{{ $category->url }}">{{ $category->display_name }}</a>
                        @empty
                            <a class="eyebrow" href="{{ $article->category->url }}">{{ $article->category->display_name }}</a>
                        @endforelse
                    </div>
                    <h1>{{ $article->title }}</h1>
                    @if($article->excerpt)<p class="article-deck">{{ $article->excerpt }}</p>@endif
                    <div class="article-byline">WORDS BY {{ strtoupper($article->author?->name ?? 'MW EDITORIAL') }} <span>·</span> {{ $article->published_at->format('d F Y') }} <span>·</span> {{ max(2, (int) ceil(str_word_count(strip_tags($article->body)) / 220)) }} MIN READ</div>
                </header>

                <div class="article-prose">
                    @if(strip_tags($article->body) !== $article->body)
                        {!! $article->body !!}
                    @else
                        @foreach(preg_split('/\n\s*\n/', $article->body) as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    @endif
                </div>

                @if($galleryImages->isNotEmpty())
                    <div class="article-gallery">
                        @foreach($galleryImages as $image)
                            <img src="{{ asset($image->path) }}" alt="{{ $image->alt_text ?? $article->title }}" loading="lazy">
                        @endforeach
                    </div>
                @endif

                @if($article->tags->isNotEmpty())
                    <div class="article-tags"><span>Tags</span>@foreach($article->tags as $tag)<span class="article-tag">{{ $tag->name }}</span>@endforeach</div>
                @endif
            </div>
        </div>
    </article>

    @if($related->isNotEmpty())
        <section class="container article-related">
            <div class="section-heading"><div><span class="eyebrow">KEEP READING</span><h2>Latest in {{ $article->category->display_name }}</h2></div></div>
            <div class="row g-4">@foreach($related as $story)<div class="col-6 col-lg-3"><x-story-card :article="$story" /></div>@endforeach</div>
        </section>
    @endif

    <nav class="container article-pagination" aria-label="More stories in this category">
        @if($previous)
            <a class="article-pagination-link article-pagination-previous" href="{{ route('articles.show', $previous) }}"><span>← Previous</span><strong>{{ $previous->title }}</strong></a>
        @else
            <span></span>
        @endif
        @if($next)
            <a class="article-pagination-link article-pagination-next" href="{{ route('articles.show', $next) }}"><span>Next →</span><strong>{{ $next->title }}</strong></a>
        @else
            <span></span>
        @endif
    </nav>

    @include('magazine.partials.newsletter')
</main>
@endsection
