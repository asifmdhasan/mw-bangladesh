<aside class="mobile-navigation-drawer" id="mobileNavigationDrawer" aria-label="Mobile navigation" aria-hidden="true" inert>
    <div class="mobile-drawer-header">
        <a class="mw-logo mobile-drawer-logo" href="{{ route('home') }}" aria-label="{{ $siteSettings['site_title'] ?? "Man's World Bangladesh" }} home">
            @if(!empty($siteSettings['site_logo']))<img src="{{ asset($siteSettings['site_logo']) }}" alt="{{ $siteSettings['site_title'] ?? "Man's World Bangladesh" }}">@else<span>MW</span><small>BANGLADESH</small>@endif
        </a>
        <button class="mobile-drawer-close" type="button" aria-label="Close navigation menu"><span aria-hidden="true">&times;</span></button>
    </div>
    <nav class="mobile-drawer-menu" aria-label="Main navigation">
        @include('partials.frontend-mobile-navigation-links', ['categories' => $navigationCategories])
    </nav>
    <div class="mobile-drawer-bottom">
        <a class="mobile-drawer-cta" href="{{ route('home') }}#newsletter">Subscribe to The MW Letter</a>
        <a class="mobile-drawer-email" href="mailto:{{ $siteSettings['contact_email'] ?? config('mail.from.address') }}"><span class="mobile-drawer-envelope" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 6.5h16v11H4z"></path><path d="m4.5 7 7.5 6 7.5-6"></path></svg></span><span>{{ $siteSettings['contact_email'] ?? config('mail.from.address') }}</span></a>
    </div>
</aside>
<button class="mobile-navigation-overlay" type="button" aria-label="Close navigation menu" tabindex="-1" hidden></button>
