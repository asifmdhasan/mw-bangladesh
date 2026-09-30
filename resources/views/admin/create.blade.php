@extends('layouts.admin')
@section('title', isset($article) ? 'Edit story' : (isset($copySource) ? 'Copy story' : 'New story'))
@section('page', 'Stories')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush
@section('content')
@php($sourceArticle = $copySource ?? ($article ?? null))
<div class="admin-page-heading"><div><span class="admin-kicker">EDITORIAL</span><h1>{{ isset($article) ? 'Edit story' : (isset($copySource) ? 'Copy story' : 'Write a story') }}</h1><p>Shape the story, choose its sections and prepare it for the magazine.</p></div></div>
<form method="POST" action="{{ isset($article) ? route('admin.articles.update',$article) : route('admin.articles.store') }}" enctype="multipart/form-data" id="storyForm">
    @csrf @if(isset($article)) @method('PUT') @endif
    @if(isset($copySource))<input type="hidden" name="copy_source_id" value="{{ $copySource->id }}">@endif
    <div class="row g-4 story-editor-layout">
        <div class="col-lg-8">
            <section class="admin-panel p-4 story-main-panel">
                <div class="mb-4"><label class="form-label" for="title">Headline</label><input class="form-control story-title-input" id="title" name="title" value="{{ old('title',$sourceArticle?->title ?? '') }}" placeholder="Add title" required maxlength="180">@error('title')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="mb-4"><label class="form-label" for="slug">Story URL slug</label><input class="form-control" id="slug" name="slug" value="{{ old('slug',$suggestedSlug ?? ($sourceArticle?->slug ?? '')) }}" data-manual="{{ old('slug') !== null ? 'true' : 'false' }}" placeholder="generated-from-headline" maxlength="180" pattern="[a-z0-9]+(-[a-z0-9]+)*" autocomplete="off"><div class="form-hint">Generated from the headline. You can edit it; spaces become hyphens.</div>@error('slug')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="mb-4"><label class="form-label" for="excerpt">Short introduction</label><textarea class="form-control" id="excerpt" name="excerpt" rows="3" maxlength="320" required>{{ old('excerpt',$sourceArticle?->excerpt ?? '') }}</textarea><div class="form-hint">A short description shown on the home page.</div>@error('excerpt')<div class="field-error">{{ $message }}</div>@enderror</div>
                <div class="mb-2"><label class="form-label" for="body">Story content</label><textarea class="form-control" id="body" name="body" required>{{ old('body',$sourceArticle?->body ?? '') }}</textarea>@error('body')<div class="field-error">{{ $message }}</div>@enderror</div>
                <input class="visually-hidden" id="storyGalleryPicker" type="file" accept="image/jpeg,image/png,image/webp,image/avif" multiple>
                <div class="form-hint">Use the editor toolbar to format text, add links, and insert images into the story.</div>
                <hr class="my-4">
                <div class="mb-2"><label class="form-label" for="images">Attach gallery to story</label><input class="form-control" id="images" name="images[]" type="file" accept="image/jpeg,image/png,image/webp,image/avif" multiple><div class="form-hint">Up to 12 JPEG, PNG, WebP or AVIF images. These appear below the cover. Use the editor’s Gallery button to place a gallery inside the story. Maximum 10 MB each.</div><div id="imagePreview" class="upload-preview" aria-live="polite"></div>@error('images')<div class="field-error">{{ $message }}</div>@enderror</div>
                @if($sourceArticle && $sourceArticle->images->isNotEmpty())<div class="d-flex gap-2 mt-3 flex-wrap">@foreach($sourceArticle->images as $image)<img src="{{ asset($image->path) }}" alt="" style="width:90px;height:65px;object-fit:cover;border-radius:6px">@endforeach</div>@endif
            </section>
        </div>
        <aside class="col-lg-4 story-editor-sidebar">
            <section class="admin-panel story-side-panel mb-3">
                <div class="story-side-heading"><strong>Publish</strong><span class="text-muted">Set story visibility</span></div>
                <div class="story-side-body">
                    @php($publishStatus = (string) old('publish', isset($article) && $article->published_at ? '1' : '0'))
                    <label class="form-check story-check"><input class="form-check-input" type="radio" id="publish-now" name="publish" value="1" @checked($publishStatus === '1')><span class="form-check-label">Publish now</span></label>
                    <label class="form-check story-check"><input class="form-check-input" type="radio" id="publish-draft" name="publish" value="0" @checked($publishStatus === '0')><span class="form-check-label">Draft</span></label>
                    <label class="form-check story-check"><input class="form-check-input" type="checkbox" id="is_spotlight" name="is_spotlight" value="1" @checked(old('is_spotlight',$article->is_spotlight ?? false))><span class="form-check-label">Show in Spotlight slider</span></label>
                    <label class="form-check story-check"><input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured',$article->is_featured ?? false))><span class="form-check-label">Editor's pick</span></label>
                </div>
                <div class="story-side-footer"><button class="btn btn-danger" type="submit">{{ isset($article) ? 'Update story' : 'Save story' }}</button></div>
            </section>
            <section class="admin-panel story-side-panel mb-3">
                <div class="story-side-heading"><strong>Featured image</strong><span class="text-muted">Shown on story cards and article page</span></div>
                <div class="story-side-body">
                    <label class="featured-image-picker" for="featured_image"><span id="featuredImagePlaceholder"><i class="bi bi-image"></i><b>Set main featured image</b><small>Recommended: 1600 × 800 px</small></span><img id="featuredImagePreview" src="{{ $sourceArticle?->featured_image ? asset($sourceArticle->featured_image) : '' }}" alt="Main featured image preview" @if(!$sourceArticle?->featured_image) hidden @endif></label>
                    <input class="visually-hidden" id="featured_image" name="featured_image" type="file" accept="image/jpeg,image/png,image/webp,image/avif"><div class="form-hint">Maximum supported size: 1.5 MB. JPEG, PNG, WebP or AVIF.</div><div class="field-error" id="featuredImageError" role="alert">@error('featured_image'){{ $message }}@enderror</div>
                    <label class="form-label mt-4" for="mobile_featured_image">Mobile featured image <span class="text-muted">(optional)</span></label>
                    <label class="featured-image-picker" for="mobile_featured_image"><span id="mobileFeaturedImagePlaceholder"><i class="bi bi-phone"></i><b>Set mobile featured image</b><small>Recommended: 1000 × 500 px</small></span><img id="mobileFeaturedImagePreview" src="{{ $sourceArticle?->mobile_featured_image ? asset($sourceArticle->mobile_featured_image) : '' }}" alt="Mobile featured image preview" @if(!$sourceArticle?->mobile_featured_image) hidden @endif></label>
                    <input class="visually-hidden" id="mobile_featured_image" name="mobile_featured_image" type="file" accept="image/jpeg,image/png,image/webp,image/avif"><div class="form-hint">Shown on mobile instead of the main image. If omitted, the main image is used. Maximum supported size: 1.5 MB.</div><div class="field-error" id="mobileFeaturedImageError" role="alert">@error('mobile_featured_image'){{ $message }}@enderror</div>
                    <label class="form-label mt-3" for="image_url">Or use image URL</label><input class="form-control" id="image_url" name="image_url" type="url" value="{{ old('image_url',$sourceArticle?->image_url ?? '') }}" placeholder="https://...">@error('image_url')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </section>
            <section class="admin-panel story-side-panel mb-3">
                <div class="story-side-heading"><strong>Categories</strong><span class="d-flex align-items-center gap-2"><button class="category-collapse-button" type="button" id="toggleCategoryPicker" aria-expanded="true" aria-label="Collapse categories"><i class="bi bi-chevron-up"></i></button><button class="category-add-button" type="button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">+ Add new</button></span></div>
                <div class="story-side-body category-picker">
                    @php($selectedCategories = old('category_ids', $sourceArticle ? $sourceArticle->categories->pluck('id')->all() : []))
                    <label class="category-search"><i class="bi bi-search"></i><input type="search" id="categorySearch" placeholder="Search Categories" autocomplete="off"></label>
                    <div class="category-tree-list" id="categoryTreeList">
                        @foreach($categoryTreeItems as $item)
                            @php($category = $item['category'])
                            <label class="category-tree-item" style="--tree-depth: {{ $item['depth'] }}" data-category-name="{{ strtolower($item['path']) }}"><input class="form-check-input category-choice" type="checkbox" data-category-id="{{ $category->id }}" @checked(in_array($category->id, $selectedCategories))><span>{{ $item['path'] }}</span></label>
                        @endforeach
                    </div>
                    @if($categories->isEmpty())<p class="form-hint mb-0">Add a category to get started.</p>@endif
                    @error('category_ids')<div class="field-error">{{ $message }}</div>@enderror @error('category_ids.*')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </section>
            <section class="admin-panel story-side-panel mb-3">
                <div class="story-side-heading"><strong>Tags</strong><span class="text-muted">Comma separated</span></div>
                <div class="story-side-body"><input class="form-control" id="tag_names" name="tag_names" value="{{ old('tag_names', $sourceArticle ? $sourceArticle->tags->pluck('name')->implode(', ') : '') }}" placeholder="Culture, design, interview"><div class="form-hint">Press comma after each tag.</div>@error('tag_names')<div class="field-error">{{ $message }}</div>@enderror</div>
            </section>
            @if(isset($article))<section class="admin-panel story-side-panel mb-3"><div class="story-side-heading"><strong>Legacy image URL</strong></div><div class="story-side-body"><p class="form-hint mb-0">Current card image: {{ $article->image_url ?: 'Uses featured image or first gallery image' }}</p></div></section>@endif
        </aside>
    </div>
</form>
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryTitle" aria-hidden="true" data-category-create-url="{{ route('admin.categories.store') }}"><div class="modal-dialog modal-dialog-centered"><div class="modal-content category-modal"><div class="modal-header"><div><span class="admin-kicker">EDITORIAL DESK</span><h2 class="modal-title" id="addCategoryTitle">Add a category</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><label class="form-label" for="newCategoryParents">Parent category</label><select class="form-select" id="newCategoryParents"><option value="">— No parent (root category) —</option>@foreach($allCategories as $parent)<option value="{{ $parent['category']->id }}">{{ $parent['path'] }}</option>@endforeach</select><label class="form-label mt-3" for="newCategoryName">Category name</label><input class="form-control" id="newCategoryName" maxlength="80" placeholder="e.g. Culture"><label class="form-label mt-3" for="newCategorySlug">Slug</label><input class="form-control" id="newCategorySlug" readonly placeholder="Generated from name"><div class="form-hint">Spaces become underscores.</div><label class="form-label mt-3" for="newCategoryDescription">Short description <span class="text-muted">(optional)</span></label><textarea class="form-control" id="newCategoryDescription" maxlength="240" rows="2"></textarea><div id="categoryError" class="field-error" role="alert"></div><div id="categorySuccess" class="field-success" role="status"></div></div><div class="modal-footer"><button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-publish" id="saveCategory">Add category</button></div></div></div></div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
<script>
$(function () {
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    if (titleInput && slugInput) {
        const slugify = (value) => value.normalize('NFKD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        let slugManuallyEdited = slugInput.dataset.manual === 'true';
        if (!slugManuallyEdited && !slugInput.value) slugInput.value = slugify(titleInput.value);
        titleInput.addEventListener('input', () => {
            if (!slugManuallyEdited) slugInput.value = slugify(titleInput.value);
        });
        slugInput.addEventListener('input', () => { slugManuallyEdited = true; });
    }

    const $body = $('#body');
    const galleryButton = function (context) {
        return $.summernote.ui.button({ contents: '<i class="bi bi-images"></i> Gallery', tooltip: 'Insert a multi-image gallery', click: function () { $('#storyGalleryPicker').trigger('click'); } }).render();
    };
    if ($body.length && $.fn.summernote) {
        $body.summernote({
            height: 520,
            placeholder: 'Write your story here…',
            buttons: { mwGallery: galleryButton },
            toolbar: [['style', ['style']], ['font', ['bold', 'italic', 'underline', 'clear']], ['fontname', ['fontname']], ['fontsize', ['fontsize']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph']], ['table', ['table']], ['insert', ['link', 'picture', 'video', 'hr', 'mwGallery']], ['view', ['fullscreen', 'codeview', 'help']]],
            callbacks: {
                onImageUpload: function (files) {
                    Array.from(files).forEach(function (file) {
                        const data = new FormData(); data.append('image', file); data.append('_token', $('meta[name="csrf-token"]').attr('content'));
                        $.ajax({ url: @json(route('admin.uploads.editor-image')), method: 'POST', data, processData: false, contentType: false, headers: { Accept: 'application/json' } })
                            .done(function (result) { $body.summernote('insertImage', result.url); })
                            .fail(function (xhr) { alert(xhr.responseJSON?.errors?.image?.[0] || 'Image upload failed. Please choose an image under 10 MB.'); });
                    });
                }
            }
        });
    }
    $('#storyGalleryPicker').on('change', async function () {
        const files = Array.from(this.files || []);
        if (!files.length) return;
        if (files.length > 12) { alert('Please choose no more than 12 images at a time.'); this.value = ''; return; }
        const urls = [];
        try {
            for (const file of files) {
                const data = new FormData();
                data.append('image', file);
                data.append('_token', $('meta[name="csrf-token"]').attr('content'));
                const result = await $.ajax({ url: @json(route('admin.uploads.editor-image')), method: 'POST', data, processData: false, contentType: false, headers: { Accept: 'application/json' } });
                urls.push(result.url);
            }
            const figures = urls.map(function (url) {
                const safeUrl = $('<div>').text(url).html().replace(/"/g, '&quot;');
                return '<figure><img src="' + safeUrl + '" alt=""></figure>';
            }).join('');
            $body.summernote('pasteHTML', '<div class="article-inline-gallery">' + figures + '</div><p><br></p>');
        } catch (xhr) {
            alert(xhr.responseJSON?.errors?.image?.[0] || 'Gallery upload failed. Check that each image is under 10 MB.');
        } finally {
            this.value = '';
        }
    });
    [
        { input: '#featured_image', preview: '#featuredImagePreview', placeholder: '#featuredImagePlaceholder', error: '#featuredImageError' },
        { input: '#mobile_featured_image', preview: '#mobileFeaturedImagePreview', placeholder: '#mobileFeaturedImagePlaceholder', error: '#mobileFeaturedImageError' },
    ].forEach(function (field) {
        $(field.input).on('change', function () {
            const file = this.files?.[0];
            $(field.error).text('');
            if (!file) return;
            if (file.size > 1536 * 1024) {
                this.value = '';
                $(field.error).text('Maximum supported size is 1.5 MB.');
                return;
            }
            $(field.preview).attr('src', URL.createObjectURL(file)).prop('hidden', false);
            $(field.placeholder).hide();
        });
    });
});
</script>
@endpush
