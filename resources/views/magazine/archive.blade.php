@extends('layouts.magazine')

@section('title', 'Archive — MW Bangladesh')

@section('content')
<main class="archive-page container">
    <header class="archive-intro">
        <span class="archive-eyebrow">THE MW COLLECTION</span>
        <h1>Stories, through the years.</h1>
        <p>Explore every story by the year and month it was published.</p>
        <div class="archive-total"><strong>{{ $articlesByMonth->flatten(1)->count() }}</strong><span>published stories</span></div>
    </header>

    @if($years->isNotEmpty())
        <section class="archive-years" aria-label="Browse stories by year">
            @foreach($years as $year)
                @php($yearCount = $articlesByMonth->filter(fn ($items, $key) => str_starts_with($key, (string) $year))->flatten(1)->count())
                <article class="archive-year" data-archive-year>
                    <button class="archive-year-button" type="button" aria-expanded="false" aria-controls="archive-months-{{ $year }}">
                        <span class="archive-year-number">{{ $year }}</span>
                        <span class="archive-year-meta">{{ $yearCount }} {{ $yearCount === 1 ? 'story' : 'stories' }} <i aria-hidden="true"></i></span>
                    </button>
                    <div class="archive-year-content" id="archive-months-{{ $year }}" hidden>
                        <div class="archive-months" aria-label="Months in {{ $year }}">
                            @for($month = 1; $month <= 12; $month++)
                                @php($monthKey = sprintf('%04d-%02d', $year, $month))
                                @php($monthArticles = $articlesByMonth->get($monthKey, collect()))
                                <button class="archive-month {{ $monthArticles->isEmpty() ? 'is-empty' : '' }}" type="button" data-archive-month="{{ $monthKey }}" aria-pressed="false" {{ $monthArticles->isEmpty() ? 'disabled' : '' }}>
                                    <span>{{ \Illuminate\Support\Carbon::create((int) $year, $month, 1)->format('M') }}</span>
                                    <small>{{ str_pad((string) $monthArticles->count(), 2, '0', STR_PAD_LEFT) }}</small>
                                </button>
                            @endfor
                        </div>
                        @for($month = 1; $month <= 12; $month++)
                            @php($monthKey = sprintf('%04d-%02d', $year, $month))
                            @php($monthArticles = $articlesByMonth->get($monthKey, collect()))
                            @if($monthArticles->isNotEmpty())
                                <section class="archive-stories" data-archive-stories="{{ $monthKey }}" hidden>
                                    <div class="archive-stories-heading"><span>{{ \Illuminate\Support\Carbon::create((int) $year, $month, 1)->format('F Y') }}</span><small>{{ $monthArticles->count() }} {{ $monthArticles->count() === 1 ? 'story' : 'stories' }}</small></div>
                                    <div class="row g-3 archive-story-grid">
                                        @foreach($monthArticles as $article)
                                            <div class="col-12 col-sm-6 col-lg-4">
                                                <article class="archive-story-card">
                                                    <a href="{{ route('articles.show', $article) }}" class="archive-story-image"><img src="{{ $article->image_src }}" alt="{{ $article->title }}" loading="lazy"></a>
                                                    <div class="archive-story-copy"><span>{{ $article->category?->display_name ?? 'STORY' }}</span><h2><a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a></h2><time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->format('M j, Y') }}</time></div>
                                                </article>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            @endif
                        @endfor
                    </div>
                </article>
            @endforeach
        </section>
    @else
        <div class="archive-empty">The archive is waiting for its first story.</div>
    @endif
</main>
@endsection
