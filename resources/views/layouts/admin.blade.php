<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title', 'Admin') · Man’s World Bangladesh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}?v={{ filemtime(public_path('css/custom.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">@if(!empty($siteSettings['site_logo']))<img src="{{ asset($siteSettings['site_logo']) }}" alt="{{ $siteSettings['site_title'] ?? "Man's World Bangladesh" }}">@else<span>MW</span><small>MAN'S WORLD <b>·</b> ADMIN</small>@endif</a>
        <div class="admin-side-label">WORKSPACE</div>
        <nav class="admin-menu">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-file-earmark-text"></i> Stories</a>
            <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-collection"></i> Categories</a>
            <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Users</a>
            <a class="{{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}" href="{{ route('admin.newsletter.index') }}"><i class="bi bi-envelope-paper"></i> Newsletter</a>
        </nav>
        <div class="admin-side-label mt-4">CONFIGURATION</div>
        <nav class="admin-menu"><a class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.settings') }}"><i class="bi bi-sliders"></i> Site settings</a><a href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View website</a></nav>
        <div class="admin-sidebar-bottom"><div class="admin-avatar">{{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}</div><div class="admin-user-meta"><strong>{{ auth('admin')->user()->name }}</strong><small>Administrator</small></div><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="admin-logout" title="Sign out"><i class="bi bi-box-arrow-right"></i></button></form></div>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar"><button class="admin-menu-toggle" type="button" data-admin-sidebar-toggle aria-label="Toggle admin menu"><i class="bi bi-list"></i></button><div class="admin-breadcrumb">Man's World <span>/</span> @yield('page','Dashboard')</div><a href="{{ route('admin.articles.create') }}" class="btn btn-danger btn-sm"><i class="bi bi-plus-lg me-1"></i> New story</a></header>
        <main class="admin-content">
            @if(session('status'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>
    @stack('scripts')
</body></html>
