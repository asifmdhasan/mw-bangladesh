@extends('layouts.admin')
@section('title','Site settings')
@section('page','Site settings')
@section('content')
<div class="admin-page-heading"><div><span class="admin-kicker">SITE CONFIGURATION</span><h1>Settings</h1><p>Manage site identity, tracking and social links.</p></div></div>
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
    <div class="admin-panel p-4 mb-4">
        <div class="settings-section-title"><i class="bi bi-globe2"></i><div><h2>Site identity</h2><p>How the magazine is presented to readers.</p></div></div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="site_title">Site title</label><input id="site_title" class="form-control" name="site_title" value="{{ old('site_title',$settings['site_title'] ?? "Man's World Bangladesh") }}" required maxlength="120"></div>
            <div class="col-md-6"><label class="form-label" for="tagline">Tagline</label><input id="tagline" class="form-control" name="tagline" value="{{ old('tagline',$settings['tagline'] ?? 'Stories with perspective.') }}" maxlength="240"></div>
        </div>
    </div>
    <div class="admin-panel p-4 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-6"><label class="form-label" for="site_logo">Site Logo</label><input class="form-control" id="site_logo" name="site_logo" type="file" accept=".jpg,.jpeg,.png,.svg,.webp,image/jpeg,image/png,image/svg+xml,image/webp"><div class="form-text">JPG, PNG, SVG or WEBP. Maximum 5 MB. Saving a new logo replaces the current one.</div></div>
            <div class="col-md-6">@if(!empty($settings['site_logo']))<img src="{{ asset($settings['site_logo']) }}" alt="Current site logo" style="max-width:220px;max-height:90px;object-fit:contain">@else<span class="text-muted">No uploaded logo. The MW fallback is in use.</span>@endif</div>
        </div>
    </div>
    <div class="admin-panel p-4 mb-4">
        <div class="settings-section-title"><i class="bi bi-image"></i><div><h2>Additional logos</h2><p>Set separate artwork for the website footer and admin panel.</p></div></div>
        <div class="row g-4">
            <div class="col-md-6"><label class="form-label" for="footer_logo">Footer logo</label><input class="form-control" id="footer_logo" name="footer_logo" type="file" accept=".jpg,.jpeg,.png,.svg,.webp,image/jpeg,image/png,image/svg+xml,image/webp"><div class="form-text">JPG, PNG, SVG or WEBP. Maximum 5 MB.</div>@if(!empty($settings['footer_logo']))<img class="mt-2" src="{{ asset($settings['footer_logo']) }}" alt="Current footer logo" style="max-width:220px;max-height:90px;object-fit:contain">@else<span class="form-text d-block mt-2">Currently using the site logo.</span>@endif</div>
            <div class="col-md-6"><label class="form-label" for="admin_logo">Admin logo</label><input class="form-control" id="admin_logo" name="admin_logo" type="file" accept=".jpg,.jpeg,.png,.svg,.webp,image/jpeg,image/png,image/svg+xml,image/webp"><div class="form-text">JPG, PNG, SVG or WEBP. Maximum 5 MB.</div>@if(!empty($settings['admin_logo']))<img class="mt-2" src="{{ asset($settings['admin_logo']) }}" alt="Current admin logo" style="max-width:220px;max-height:90px;object-fit:contain">@else<span class="form-text d-block mt-2">Currently using the site logo.</span>@endif</div>
        </div>
    </div>
    <div class="admin-panel p-4 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-6"><label class="form-label" for="favicon">Favicon</label><input class="form-control" id="favicon" name="favicon" type="file" accept=".ico,.png,.svg,.webp,image/x-icon,image/vnd.microsoft.icon,image/png,image/svg+xml,image/webp"><div class="form-text">ICO, PNG, SVG or WEBP. Maximum 2 MB. Used as the browser tab icon.</div></div>
            <div class="col-md-6">@if(!empty($settings['favicon']))<img src="{{ asset($settings['favicon']) }}" alt="Current favicon" style="width:48px;height:48px;object-fit:contain"><span class="form-text d-block">Current favicon</span>@else<span class="text-muted">No favicon uploaded.</span>@endif</div>
        </div>
    </div>
    <div class="admin-panel p-4 mb-4">
        <div class="settings-section-title"><i class="bi bi-graph-up-arrow"></i><div><h2>Analytics & advertising</h2><p>Enter IDs from your Google accounts. Tracking loads on public magazine pages.</p></div></div>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="ga_measurement_id">Google Analytics 4 Measurement ID</label><input id="ga_measurement_id" class="form-control" name="ga_measurement_id" value="{{ old('ga_measurement_id',$settings['ga_measurement_id'] ?? '') }}" placeholder="G-XXXXXXXXXX"><div class="form-text">Find it in Google Analytics → Admin → Data streams.</div></div>
            <div class="col-md-6"><label class="form-label" for="adsense_publisher_id">AdSense publisher ID</label><input id="adsense_publisher_id" class="form-control" name="adsense_publisher_id" value="{{ old('adsense_publisher_id',$settings['adsense_publisher_id'] ?? '') }}" placeholder="ca-pub-1234567890123456"><div class="form-text">Ad serving also requires an approved AdSense account and ad units.</div></div>
        </div>
    </div>
    <div class="admin-panel p-4 mb-4">
        <div class="settings-section-title"><i class="bi bi-share"></i><div><h2>Social profiles</h2><p>Links used in the site footer.</p></div></div>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label" for="facebook_url">Facebook URL</label><input id="facebook_url" class="form-control" name="facebook_url" value="{{ old('facebook_url',$settings['facebook_url'] ?? '') }}" placeholder="https://facebook.com/..."></div>
            <div class="col-md-4"><label class="form-label" for="instagram_url">Instagram URL</label><input id="instagram_url" class="form-control" name="instagram_url" value="{{ old('instagram_url',$settings['instagram_url'] ?? '') }}" placeholder="https://instagram.com/..."></div>
            <div class="col-md-4"><label class="form-label" for="youtube_url">YouTube URL</label><input id="youtube_url" class="form-control" name="youtube_url" value="{{ old('youtube_url',$settings['youtube_url'] ?? '') }}" placeholder="https://youtube.com/..."></div>
        </div>
    </div>
    <button class="btn btn-danger btn-lg"><i class="bi bi-check2 me-2"></i>Save settings</button>
</form>
@endsection
