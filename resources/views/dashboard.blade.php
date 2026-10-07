<x-app-layout>
    <x-slot name="header"><h1 class="h3 mb-0">{{ __('Your account') }}</h1></x-slot>
    <div class="card shadow-sm"><div class="card-body p-4"><h2 class="h5">Welcome, {{ Auth::user()->name }}.</h2><p class="text-secondary mb-3">Your subscriber account is ready. Explore the latest stories from MW Bangladesh.</p><a class="btn btn-dark" href="{{ route('home') }}">Browse the magazine</a></div></div>
</x-app-layout>
