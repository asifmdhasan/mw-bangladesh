@extends('layouts.magazine')
@section('title', "MW Bangladesh — Style, Culture & Ideas")
@section('content')
<main>
    <section class="spotlight-section" id="spotlight" aria-label="Spotlight stories">
        <div id="spotlightCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6500">
            <div class="carousel-indicators">
                @foreach($spotlight as $index => $article)<button type="button" data-bs-target="#spotlightCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" @if($index === 0) aria-current="true" @endif aria-label="Slide {{ $index + 1 }}"></button>@endforeach
            </div>
            <div class="carousel-inner">
                @forelse($spotlight as $index => $article)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}"><a class="spotlight-slide" href="{{ route('articles.show', $article) }}"><picture class="spotlight-media"><source media="(max-width: 767.98px)" srcset="{{ $article->mobile_image_src }}"><img src="{{ $article->image_src }}" alt=""></picture><span class="spotlight-shade"></span><span class="spotlight-caption"><span class="spotlight-kicker">SPOTLIGHT <i>·</i> {{ strtoupper($article->category->display_name) }}</span><strong>{{ $article->title }}</strong><span class="spotlight-read">Read story <b>↗</b></span></span></a></div>
                @empty
                    <div class="carousel-item active"><div class="spotlight-empty">Stories are coming soon.</div></div>
                @endforelse
            </div>
            @if($spotlight->count() > 1)<button class="carousel-control-prev" type="button" data-bs-target="#spotlightCarousel" data-bs-slide="prev" aria-label="Previous story"><span class="carousel-control-prev-icon" aria-hidden="true"></span></button><button class="carousel-control-next" type="button" data-bs-target="#spotlightCarousel" data-bs-slide="next" aria-label="Next story"><span class="carousel-control-next-icon" aria-hidden="true"></span></button>@endif
        </div>
    </section>

    <section class="container story-section" id="latest">
        <div class="section-heading"><h2>Latest</h2><a href="{{ route('articles.index') }}">View all <span>›</span></a></div>
        <div class="row g-4 story-grid">
            @foreach($latest as $article)<div class="col-6 col-lg-3"><x-story-card :article="$article" /></div>@endforeach
            <div class="col-6 col-lg-3"><a class="see-all-card" href="{{ route('articles.index') }}"><span class="see-all-arrow">›</span><strong>See All</strong></a></div>
        </div>
    </section>

    @foreach($sections as $section)
        @php($sectionId = strtolower($section['title']))
        <section class="container story-section" id="{{ $sectionId }}">
            <div class="section-heading"><h2>{{ $section['title'] }}</h2><a href="{{ $section['category'] ? $section['category']->url : route('articles.index') }}">View all <span>›</span></a></div>
            <div class="row g-4 story-grid">
                @foreach($section['articles'] as $article)<div class="col-6 col-lg-3"><x-story-card :article="$article" /></div>@endforeach
                <div class="col-6 col-lg-3"><a class="see-all-card" href="{{ $section['category'] ? $section['category']->url : route('articles.index') }}"><span class="see-all-arrow">›</span><strong>See All</strong></a></div>
            </div>
        </section>
    @endforeach

    @include('magazine.partials.newsletter')
</main>
@endsection
