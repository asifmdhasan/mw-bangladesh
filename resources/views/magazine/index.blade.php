@extends('layouts.magazine')
@section('title', isset($category) ? $category->name.' — Man\'s World Bangladesh' : 'Stories — Man\'s World Bangladesh')
@section('content')
<main class="container listing-page"><div class="eyebrow">THE MW JOURNAL</div><div class="listing-title-row"><div><h1>{{ isset($category) ? $category->name : 'Stories worth your time' }}</h1><p>{{ isset($category) ? $category->description : 'Fresh perspective across the things that make a life.' }}</p></div><form class="listing-search" action="{{ route('articles.index') }}"><input name="q" value="{{ request('q') }}" placeholder="Search stories"><button>Search</button></form></div>
<div class="category-pills"><a href="{{ route('articles.index') }}">All stories</a>@foreach($categories as $item)<a class="{{ isset($category) && $category->id === $item->id ? 'active' : '' }}" href="{{ route('categories.show', $item) }}">{{ $item->name }}</a>@endforeach</div>
<div class="row g-4">@forelse($articles as $article)<div class="col-md-6 col-lg-4"><x-story-card :article="$article" /></div>@empty<div class="col-12 py-5"><p>No stories found. Try another search.</p></div>@endforelse</div><div class="mt-5">{{ $articles->links() }}</div></main>
@endsection
