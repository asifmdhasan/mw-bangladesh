<section>
    <header class="mb-3"><h2 class="h5 text-danger">{{ __('Delete account') }}</h2><p class="text-secondary small mb-0">{{ __('Deleting your account permanently removes your profile and account data.') }}</p></header>
    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">{{ __('Delete account') }}</button>
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" maxWidth="md">
        <form method="post" action="{{ route('profile.destroy') }}">@csrf @method('delete')
            <div class="modal-header"><h2 class="modal-title fs-5" id="confirm-user-deletion-title">{{ __('Are you sure?') }}</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body"><p>{{ __('Enter your password to permanently delete your account.') }}</p><x-input-label for="delete_password" :value="__('Password')"/><x-text-input id="delete_password" name="password" type="password" placeholder="{{ __('Password') }}"/><x-input-error :messages="$errors->userDeletion->get('password')"/></div>
            <div class="modal-footer"><x-secondary-button data-bs-dismiss="modal">{{ __('Cancel') }}</x-secondary-button><x-danger-button>{{ __('Delete account') }}</x-danger-button></div>
        </form>
    </x-modal>
</section>
