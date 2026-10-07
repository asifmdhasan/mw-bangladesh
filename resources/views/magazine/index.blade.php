@extends('layouts.magazine')
@section('title', isset($category) ? $category->display_name.' — MW Bangladesh' : 'Stories — MW Bangladesh')
@section('content')
<main class="container listing-page {{ isset($category) ? 'pt-0' : '' }}"><div class="listing-title-row"><div><h1>{{ isset($category) ? $category->display_name : 'Stories worth your time' }}</h1><p>{{ isset($category) ? $category->description : 'Fresh perspective across the things that make a life.' }}</p></div></div>
@unless(isset($category))
<div class="category-pills"><a href="{{ route('articles.index') }}">All stories</a>@foreach($categories as $item)<span class="category-pill-group"><a class="category-root-pill {{ isset($category) && $category->id === $item->id ? 'active' : '' }}" href="{{ $item->url }}">{{ $item->name }}</a>@foreach($item->children as $child)<a class="category-child-pill {{ isset($category) && $category->id === $child->id ? 'active' : '' }}" href="{{ $child->url }}">{{ $child->name }}</a>@endforeach</span>@endforeach</div>
@endunless
<div class="row g-4">@forelse($articles as $article)<div class="col-md-6 col-lg-4"><x-story-card :article="$article" /></div>@empty<div class="col-12 py-5"><p>No stories found. Try another search.</p></div>@endforelse</div><div class="mt-5">{{ $articles->links('pagination::bootstrap-5') }}</div></main>
@endsection
