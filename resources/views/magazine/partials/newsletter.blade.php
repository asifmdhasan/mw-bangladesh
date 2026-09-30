<section class="newsletter-band" id="newsletter">
    <div class="container newsletter-inner">
        <div><span class="newsletter-kicker">THE MW LETTER</span><h2>Want more premium lifestyle content?<br>Subscribe now!</h2></div>
        @if(session('newsletter_success'))
            <strong class="newsletter-success" role="status">{{ session('newsletter_success') }}</strong>
        @else
            <form class="newsletter-form" method="post" action="{{ route('newsletter.subscribe') }}">
                @csrf
                <input type="text" name="name" placeholder="Name" aria-label="Name">
                <input type="email" name="email" placeholder="Email address" aria-label="Email address" required>
                @error('email')<small class="newsletter-error">{{ $message }}</small>@enderror
                <button type="submit">Subscribe</button>
            </form>
        @endif
    </div>
</section>
