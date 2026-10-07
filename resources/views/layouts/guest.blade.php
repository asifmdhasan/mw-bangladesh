<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!empty($siteSettings['favicon']))<link rel="icon" href="{{ asset($siteSettings['favicon']) }}">@endif
    <title>{{ config('app.name', 'MW Bangladesh') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" rel="stylesheet">
    @if(!empty($siteSettings['ga_measurement_id']))<script async src="https://www.googletagmanager.com/gtag/js?id={{ rawurlencode($siteSettings['ga_measurement_id']) }}"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json($siteSettings['ga_measurement_id']));</script>@endif
    @if(!empty($siteSettings['adsense_publisher_id']))<meta name="google-adsense-account" content="{{ $siteSettings['adsense_publisher_id'] }}"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ rawurlencode($siteSettings['adsense_publisher_id']) }}" crossorigin="anonymous"></script>@endif
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center p-3">
        <a class="mw-logo mb-4" href="{{ route('home') }}" aria-label="MW Bangladesh">@if(!empty($siteSettings['site_logo']))<img src="{{ asset($siteSettings['site_logo']) }}" alt="{{ $siteSettings['site_title'] ?? "MW Bangladesh" }}">@else<span>MW</span><small>BANGLADESH</small>@endif</a>
        <div class="auth-panel w-100">{{ $slot }}</div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>
