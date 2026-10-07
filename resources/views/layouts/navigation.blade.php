<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="mw-logo" href="{{ route('dashboard') }}" aria-label="MW Bangladesh">@if(!empty($siteSettings['site_logo']))<img src="{{ asset($siteSettings['site_logo']) }}" alt="{{ $siteSettings['site_title'] ?? "MW Bangladesh" }}">@else<span>MW</span><small>BANGLADESH</small>@endif</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#subscriberNav" aria-controls="subscriberNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="subscriberNav">
            <div class="navbar-nav ms-lg-4 me-auto"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a><a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">Profile</a><a class="nav-link" href="{{ route('home') }}">Read the magazine</a></div>
            <div class="dropdown"><button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">{{ Auth::user()->name }}</button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile settings</a></li><li><hr class="dropdown-divider"></li><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item" type="submit">Sign out</button></form></li></ul></div>
        </div>
    </div>
</nav>
