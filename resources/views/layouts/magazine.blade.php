<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $siteSettings['tagline'] ?? 'MW Bangladesh — stories on style, culture, entertainment and ideas.' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!empty($siteSettings['favicon']))<link rel="icon" href="{{ asset($siteSettings['favicon']) }}">@endif
    <title>@yield('title', ($siteSettings['site_title'] ?? "MW Bangladesh").' — A life well considered')</title>
    @yield('meta')
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" rel="stylesheet">
    @if(!empty($siteSettings['ga_measurement_id']))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ rawurlencode($siteSettings['ga_measurement_id']) }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json($siteSettings['ga_measurement_id']));</script>
    @endif
    @if(!empty($siteSettings['adsense_publisher_id']))<meta name="google-adsense-account" content="{{ $siteSettings['adsense_publisher_id'] }}"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ rawurlencode($siteSettings['adsense_publisher_id']) }}" crossorigin="anonymous"></script>@endif
</head>
<body>
    @include('partials.frontend-mobile-navigation')
    <div class="mobile-site-shell" id="mobileSiteShell">
    <header class="site-header">
        <div class="container brand-row"><a class="mw-logo" href="{{ route('home') }}" aria-label="MW Bangladesh">@if(!empty($siteSettings['site_logo']))<img src="{{ asset($siteSettings['site_logo']) }}" alt="{{ $siteSettings['site_title'] ?? "MW Bangladesh" }}">@else<span>MW</span><small>BANGLADESH</small>@endif</a><div class="header-actions">
            <form class="nav-search d-none d-md-flex" action="{{ route('articles.index') }}" data-search-url="{{ route('search.suggestions') }}" role="search">
                <input name="q" type="search" aria-label="Search" aria-autocomplete="list" aria-controls="nav-search-results" aria-expanded="false" autocomplete="off" placeholder="Search">
                <button type="submit" aria-label="Search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg></button>
                <div class="nav-search-results" id="nav-search-results" role="listbox" hidden></div>
            </form>
            <button class="mobile-nav-trigger" id="mobileNavTrigger" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobileNavigationDrawer"><span></span><span></span><span></span></button>
        </div></div>
        <nav class="navbar navbar-expand-lg nav-strip"><div class="container"><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse justify-content-center" id="mainNav"><div class="navbar-nav">
            @foreach($navigationCategories as $category)
                @if($category->children->isEmpty())
                    <a class="nav-link" href="{{ $category->url }}">{{ $category->name }}</a>
                @else
                    <div class="nav-item dropdown category-navigation">
                        <a class="nav-link category-nav-parent {{ isset($activeParentCategory) && $activeParentCategory->is($category) ? 'active' : '' }}" href="{{ $category->url }}">{{ $category->name }}</a>
                        <button class="category-nav-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Show {{ $category->name }} categories"><span class="visually-hidden">Toggle {{ $category->name }} categories</span></button>
                        <div class="dropdown-menu category-nav-menu">
                            @foreach($category->children as $child)
                                <a class="dropdown-item category-nav-child" href="{{ $child->url }}">{{ $child->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div></div></div></nav>
    </header>
    @if(isset($activeParentCategory) && $activeParentCategory->children->isNotEmpty())
    <div class="container category-content-container">
        <div class="category-pills category-archive-subnav" aria-label="{{ $activeParentCategory->name }} subcategories">
            @foreach($activeParentCategory->children as $child)
                <a class="category-child-pill" href="{{ $child->url }}">{{ $child->name }}</a>
            @endforeach
        </div>
    </div>
    @endif
    @if(session('status'))<div class="container pt-3"><div class="alert alert-success py-2 mb-0">{{ session('status') }}</div></div>@endif
    @yield('content')
    <footer class="site-footer">
        <div class="container footer-main">
            <div class="footer-brand">
                <a class="mw-logo mw-logo-footer" href="{{ route('home') }}">
                    @if(!empty($siteSettings['footer_logo'] ?? $siteSettings['site_logo'] ?? null))
                        <img src="{{ asset($siteSettings['footer_logo'] ?? $siteSettings['site_logo'] ?? null) }}" alt="{{ $siteSettings['site_title'] ?? "MW Bangladesh" }}">
                    @else
                        <span>MW</span><small>BANGLADESH</small>
                    @endif
                </a>
            </div>
            <div class="footer-columns">
                <section class="footer-explore">
                    <h3>LATEST</h3>
                    <div class="footer-category-columns">
                        @php($categoryColumns = $navigationCategories->chunk(max(1, (int) ceil($navigationCategories->count() / 2))))
                        @foreach($categoryColumns as $columnIndex => $categoryColumn)
                            <div class="footer-link-column">
                                @foreach($categoryColumn as $category)<a href="{{ $category->url }}">{{ $category->name }}</a>@endforeach
                            </div>
                        @endforeach
                    </div>
                </section>
                <section class="footer-company">
                    <h3>{{ $siteSettings['site_title'] ?? "MW Bangladesh" }}</h3>
                    <div class="footer-company-columns">
                        <div class="footer-link-column">
                            <a href="{{ route('home') }}">About</a>
                            <a href="{{ route('home') }}#newsletter">Contact</a>
                            <a href="{{ route('home') }}#newsletter">Advertise</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div class="footer-bottom">
            <p class="footer-copyright">© 2026 MW Bangladesh. All rights reserved.</p>
            <div class="footer-social">
                <a href="{{ $siteSettings['facebook_url'] ?? '#' }}" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13.25 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.5 1.5-1.5h1.7V3.9c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.2V10H7.3v3h2.75v8z"/></svg>
                </a>
                <a href="{{ $siteSettings['youtube_url'] ?? '#' }}" aria-label="YouTube">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8Z"/><path fill="#E72429" d="m9.6 15.7 6.3-3.7-6.3-3.7z"/></svg>
                </a>
                <a href="{{ $siteSettings['instagram_url'] ?? '#' }}" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="#fff" stroke-width="2.2"/><circle cx="12" cy="12" r="4.2" fill="none" stroke="#fff" stroke-width="2.2"/><circle cx="17.7" cy="6.5" r="1.3" fill="#fff"/></svg>
                </a>
            </div>
        </div>
    </footer>
    </div>
    <div class="mobile-search-panel" id="mobileSearchPanel" hidden>
        <button class="mobile-search-close" type="button" aria-label="Close search">&times;</button>
        <form class="nav-search mobile-search-form d-flex" action="{{ route('articles.index') }}" data-search-url="{{ route('search.suggestions') }}" role="search">
            <input name="q" type="search" aria-label="Search" aria-autocomplete="list" aria-controls="mobile-nav-search-results" aria-expanded="false" autocomplete="off" placeholder="Search">
            <button type="submit" aria-label="Search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg></button>
            <div class="nav-search-results" id="mobile-nav-search-results" role="listbox" hidden></div>
        </form>
    </div>
    <nav class="mobile-bottom-navigation" aria-label="Mobile quick navigation">
        <button type="button" id="mobileBottomMenu" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNavigationDrawer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <a href="{{ route('home') }}" aria-label="Home"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg></a>
        <button type="button" id="mobileBottomSearch" aria-label="Open search" aria-expanded="false" aria-controls="mobileSearchPanel"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 5 5"/></svg></button>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body></html>
