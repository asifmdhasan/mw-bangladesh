@props(['article'])
<article class="story-card">
    <a class="story-card-link" href="{{ route('articles.show', $article) }}" aria-label="Read {{ $article->title }}">
        <div class="story-image"><img src="{{ $article->image_src }}" alt="{{ $article->images->first()?->alt_text ?? $article->title }}" loading="lazy"><span class="story-image-shade"></span></div>
        <div class="story-copy"><span class="story-category">{{ strtoupper($article->category->name) }}</span><h3 class="story-title">{{ $article->title }}</h3></div>
    </a>
</article>
