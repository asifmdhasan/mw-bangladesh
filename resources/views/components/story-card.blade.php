@props(['article'])
<article class="story-card">
    <a class="story-card-link" href="{{ route('articles.show', $article) }}" aria-label="Read {{ $article->title }}">
        <div class="story-image"><picture><source media="(max-width: 767.98px)" srcset="{{ $article->mobile_image_src }}"><img src="{{ $article->image_src }}" alt="{{ $article->images->first()?->alt_text ?? $article->title }}" loading="lazy"></picture><span class="story-image-shade"></span></div>
        <div class="story-copy"><span class="story-category">{{ strtoupper($article->category->display_name) }}</span><h3 class="story-title">{{ $article->title }}</h3></div>
    </a>
</article>
