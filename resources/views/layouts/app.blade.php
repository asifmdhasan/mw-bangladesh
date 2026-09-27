<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Man\'s World BD') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" rel="stylesheet">
    @if(!empty($siteSettings['ga_measurement_id']))<script async src="https://www.googletagmanager.com/gtag/js?id={{ rawurlencode($siteSettings['ga_measurement_id']) }}"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json($siteSettings['ga_measurement_id']));</script>@endif
    @if(!empty($siteSettings['adsense_publisher_id']))<meta name="google-adsense-account" content="{{ $siteSettings['adsense_publisher_id'] }}"><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ rawurlencode($siteSettings['adsense_publisher_id']) }}" crossorigin="anonymous"></script>@endif
</head>
<body>
    <div class="min-vh-100 bg-light">
        @include('layouts.navigation')
        @isset($header)<header class="bg-white border-bottom"><div class="container py-4">{{ $header }}</div></header>@endisset
        <main class="container py-4">{{ $slot }}</main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>
