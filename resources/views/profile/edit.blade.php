<x-app-layout>
    <x-slot name="header"><h1 class="h3 mb-0">Profile settings</h1></x-slot>
    <div class="row g-4">
        <div class="col-12"><div class="card shadow-sm"><div class="card-body p-4">@include('profile.partials.update-profile-information-form', ['user' => $user])</div></div></div>
        <div class="col-12"><div class="card shadow-sm"><div class="card-body p-4">@include('profile.partials.update-password-form')</div></div></div>
        <div class="col-12"><div class="card border-danger shadow-sm"><div class="card-body p-4">@include('profile.partials.delete-user-form')</div></div></div>
    </div>
</x-app-layout>
