<section>
    <header class="mb-4"><h2 class="h5">{{ __('Update password') }}</h2><p class="text-secondary small mb-0">{{ __('Choose a strong password to keep your account secure.') }}</p></header>
    <form method="post" action="{{ route('password.update') }}" class="vstack gap-3">@csrf @method('put')
        <div><x-input-label for="update_password_current_password" :value="__('Current password')"/><x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"/><x-input-error :messages="$errors->updatePassword->get('current_password')"/></div>
        <div><x-input-label for="update_password_password" :value="__('New password')"/><x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password"/><x-input-error :messages="$errors->updatePassword->get('password')"/></div>
        <div><x-input-label for="update_password_password_confirmation" :value="__('Confirm password')"/><x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"/><x-input-error :messages="$errors->updatePassword->get('password_confirmation')"/></div>
        <div class="d-flex align-items-center gap-3"><x-primary-button>{{ __('Update password') }}</x-primary-button>@if(session('status')==='password-updated')<span class="small text-success">{{ __('Saved.') }}</span>@endif</div>
    </form>
</section>
