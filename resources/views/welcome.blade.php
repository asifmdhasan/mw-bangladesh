@extends('layouts.magazine')
@section('title', "MW Bangladesh — A life well considered")
@section('content')
<main class="container listing-page text-center">
    <span class="eyebrow">MW Bangladesh</span>
    <h1 class="mt-3">A life well considered.</h1>
    <p class="text-muted mt-3">Original stories on style, culture and the things worth your time.</p>
    <a class="btn btn-dark mt-3 px-4 py-3" href="{{ route('articles.index') }}">Explore the journal</a>
</main>
@endsection
