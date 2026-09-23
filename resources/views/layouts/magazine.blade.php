<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $siteSettings['tagline'] ?? 'Man\'s World Bangladesh — stories on style, culture, entertainment and ideas.' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', ($siteSettings['site_title'] ?? "Man's World Bangladesh").' — A life well considered')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" rel="stylesheet">
    @if(!empty($siteSettings['ga_measurement_id']))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ rawurlencode($siteSettings['ga_measurement_id']) }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json($siteSettings['ga_measurement_id']));</script>
    @endif
    @if(!empty($siteSettings['adsense_publisher_id']))<meta name="google-adsense-account" content="{{ $siteSettings['adsense_publisher_id'] }}"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ rawurlencode($siteSettings['adsense_publisher_id']) }}" crossorigin="anonymous"></script>@endif
</head>
<body>
    <header class="site-header">
        <div class="container brand-row"><a class="mw-logo" href="{{ route('home') }}" aria-label="Man's World Bangladesh"><span>MW</span><small>BANGLADESH</small></a><div class="account-links">
            @auth<a href="{{ route('dashboard') }}">My account</a>@else<a href="{{ route('login') }}">Sign in</a>@endauth
            <a class="subscribe-link" href="{{ route('home') }}#newsletter">Subscribe</a>
        </div></div>
        <nav class="navbar navbar-expand-lg nav-strip"><div class="container"><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse justify-content-center" id="mainNav"><div class="navbar-nav">
            <a class="nav-link" href="{{ route('home') }}#spotlight">Spotlight</a><a class="nav-link" href="{{ route('home') }}#latest">Latest</a><a class="nav-link" href="{{ route('home') }}#style">Style</a><a class="nav-link" href="{{ route('home') }}#entertainment">Entertainment</a><a class="nav-link" href="{{ route('home') }}#magazine">Magazine</a><a class="nav-link" href="{{ route('home') }}#newsletter">Subscribe</a>
        </div></div><form class="nav-search d-none d-lg-flex" action="{{ route('articles.index') }}"><input name="q" aria-label="Search stories" placeholder="Search stories"><button aria-label="Search">⌕</button></form></div></nav>
    </header>
    @if(session('status') || session('newsletter_success'))<div class="container pt-3"><div class="alert alert-success py-2 mb-0">{{ session('status') ?? session('newsletter_success') }}</div></div>@endif
    @yield('content')
    <footer class="site-footer"><div class="container footer-main"><div><a class="mw-logo mw-logo-footer" href="{{ route('home') }}"><span>MW</span><small>BANGLADESH</small></a></div><div><h3>Explore</h3><a href="{{ route('home') }}#latest">Latest</a><a href="{{ route('home') }}#style">Style</a><a href="{{ route('home') }}#entertainment">Entertainment</a><a href="{{ route('home') }}#magazine">Magazine</a></div><div><h3>{{ $siteSettings['site_title'] ?? "Man's World Bangladesh" }}</h3><a href="{{ route('home') }}">About</a><a href="{{ route('home') }}#newsletter">Contact</a><a href="{{ route('home') }}#newsletter">Advertise</a></div><div><h3>Follow</h3><a href="{{ $siteSettings['facebook_url'] ?? '#' }}">Facebook</a><a href="{{ $siteSettings['instagram_url'] ?? '#' }}">Instagram</a><a href="{{ $siteSettings['youtube_url'] ?? '#' }}">YouTube</a></div></div><div class="footer-bottom"><span>© {{ date('Y') }} {{ $siteSettings['site_title'] ?? "Man's World Bangladesh" }}</span><span>{{ $siteSettings['tagline'] ?? 'Stories with perspective.' }}</span><div class="footer-social"><a href="{{ $siteSettings['facebook_url'] ?? '#' }}">FACEBOOK</a><a href="{{ $siteSettings['instagram_url'] ?? '#' }}">INSTAGRAM</a><a href="{{ $siteSettings['youtube_url'] ?? '#' }}">YOUTUBE</a></div></div></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body></html>
