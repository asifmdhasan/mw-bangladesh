<section>
    <header class="mb-4"><h2 class="h5">{{ __('Profile information') }}</h2><p class="text-secondary small mb-0">{{ __('Update your account name and email address.') }}</p></header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
    <form method="post" action="{{ route('profile.update') }}" class="vstack gap-3">@csrf @method('patch')
        <div><x-input-label for="name" :value="__('Name')"/><x-text-input id="name" name="name" type="text" :value="old('name',$user->name)" required autofocus autocomplete="name"/><x-input-error :messages="$errors->get('name')"/></div>
        <div><x-input-label for="email" :value="__('Email')"/><x-text-input id="email" name="email" type="email" :value="old('email',$user->email)" required autocomplete="username"/><x-input-error :messages="$errors->get('email')"/>
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())<p class="small mt-2">{{ __('Your email address is unverified.') }} <button form="send-verification" class="btn btn-link btn-sm p-0">{{ __('Resend verification email') }}</button></p>@endif
        </div>
        <div class="d-flex align-items-center gap-3"><x-primary-button>{{ __('Save profile') }}</x-primary-button>@if(session('status')==='profile-updated')<span class="small text-success">{{ __('Saved.') }}</span>@endif</div>
    </form>
</section>
