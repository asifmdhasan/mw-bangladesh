@extends('layouts.admin')
@section('title','Categories')
@section('page','Categories')
@section('content')
<div class="admin-page-heading"><div><span class="admin-kicker">CONTENT ORGANIZATION</span><h1>Categories</h1><p>Build a clear parent and child category structure.</p></div></div>
<div class="row g-4">
    <div class="col-lg-4"><div class="admin-panel p-4"><h2 class="admin-form-title">Add category</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}">@csrf
            <div class="mb-3"><label class="form-label" for="create-category-parent">Parent category</label><select class="form-select" id="create-category-parent" name="parent_id"><option value="">— No parent (root category) —</option>@foreach($allCategories as $option)<option value="{{ $option['category']->id }}" @selected(old('parent_id') == $option['category']->id)>{{ $option['path'] }}</option>@endforeach</select>@error('parent_id')<div class="field-error">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label" for="create-category-name">Category name</label><input class="form-control" id="create-category-name" name="name" value="{{ old('name') }}" maxlength="80" required placeholder="e.g. Culture">@error('name')<div class="field-error">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label" for="create-category-slug">Slug</label><input class="form-control" id="create-category-slug" value="{{ old('slug') }}" readonly placeholder="Generated from category name"><div class="form-hint">Generated from the name. Spaces become underscores.</div></div>
            <div class="mb-3"><label class="form-label" for="create-category-description">Description</label><textarea class="form-control" id="create-category-description" name="description" rows="3" maxlength="240">{{ old('description') }}</textarea></div>
            <button class="btn btn-danger">Create category</button>
        </form>
    </div></div>
    <div class="col-lg-8"><div class="admin-panel"><div class="admin-panel-head"><div><h2>Category hierarchy</h2><p>{{ count($categories) }} categories</p></div></div>
        <div class="table-responsive"><table class="table admin-table align-middle mb-0"><thead><tr><th>Category</th><th>Stories</th><th>Slug</th><th></th></tr></thead><tbody>
            @forelse($categories as $item)
                @php($category = $item['category'])
                <tr><td><div class="category-hierarchy-name" style="--tree-depth: {{ $item['depth'] }}"><strong>{{ $category->name }}</strong>@if($item['depth'] > 0)<small>{{ $item['path'] }}</small>@endif @if($category->description)<small>{{ $category->description }}</small>@endif</div></td><td>{{ $category->articles_count }}</td><td><code>{{ $category->slug }}</code></td><td class="text-end text-nowrap"><button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#edit-category-{{ $category->id }}" aria-label="Edit {{ $item['path'] }}"><i class="bi bi-pencil"></i></button> <form class="d-inline" method="POST" action="{{ route('admin.categories.destroy',$category) }}" data-confirm="Delete this category?">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" aria-label="Delete {{ $item['path'] }}"><i class="bi bi-trash"></i></button></form></td></tr>
                <tr class="collapse" id="edit-category-{{ $category->id }}"><td colspan="4"><form method="POST" action="{{ route('admin.categories.update',$category) }}" class="category-edit-form">@csrf @method('PUT')
                    <div><label class="form-label">Category name</label><input class="form-control form-control-sm" name="name" value="{{ $category->name }}" maxlength="80" required></div>
                    <div><label class="form-label">Parent category</label><select class="form-select form-select-sm" name="parent_id"><option value="">— No parent (root category) —</option>@foreach($allCategories as $option)@if($option['category']->id !== $category->id && !in_array($option['category']->id, $item['descendant_ids']))<option value="{{ $option['category']->id }}" @selected($category->parent_id == $option['category']->id)>{{ $option['path'] }}</option>@endif @endforeach</select>@error('parent_id')<div class="field-error">{{ $message }}</div>@enderror</div>
                    <div><label class="form-label">Slug</label><input class="form-control form-control-sm category-slug-preview" value="{{ $category->slug }}" readonly></div>
                    <div><label class="form-label">Description</label><input class="form-control form-control-sm" name="description" value="{{ $category->description }}" maxlength="240"></div>
                    <div class="category-edit-actions"><button class="btn btn-dark btn-sm">Save category</button></div>
                </form></td></tr>
            @empty<tr><td colspan="4" class="text-center py-5">No categories found.</td></tr>@endforelse
        </tbody></table></div>
    </div></div>
</div>
@endsection
