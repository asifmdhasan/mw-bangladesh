@extends('layouts.admin')
@section('title','Categories')
@section('page','Categories')
@section('content')
<div class="admin-page-heading"><div><span class="admin-kicker">CONTENT ORGANIZATION</span><h1>Categories</h1><p>Create sections, set URL slugs, and assign one or more parent categories.</p></div></div>
<div class="row g-4">
    <div class="col-lg-4"><div class="admin-panel p-4"><h2 class="admin-form-title">Add category</h2><form method="POST" action="{{ route('admin.categories.store') }}">@csrf
        <div class="mb-3"><label class="form-label" for="create-category-name">Name</label><input class="form-control" id="create-category-name" name="name" value="{{ old('name') }}" maxlength="80" required placeholder="e.g. Culture"></div>
        <div class="mb-3"><label class="form-label" for="create-category-slug">Slug</label><input class="form-control" id="create-category-slug" value="{{ old('slug') }}" readonly placeholder="Generated from category name"><div class="form-hint">Generated from the name. Spaces become underscores.</div></div>
        <div class="mb-3"><label class="form-label" for="create-category-parents">Parent categories</label><select class="form-select category-parent-select" id="create-category-parents" name="parent_ids[]" multiple>@foreach($allCategories as $parent)<option value="{{ $parent->id }}" @selected(in_array($parent->id, old('parent_ids', [])))>{{ $parent->name }}</option>@endforeach</select><div class="form-hint">Select more than one with Ctrl or Command.</div>@error('parent_ids')<div class="field-error">{{ $message }}</div>@enderror @error('parent_ids.*')<div class="field-error">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label class="form-label" for="create-category-description">Description</label><textarea class="form-control" id="create-category-description" name="description" rows="3" maxlength="240">{{ old('description') }}</textarea></div><button class="btn btn-danger">Create category</button>
    </form></div></div>
    <div class="col-lg-8"><div class="admin-panel"><div class="admin-panel-head"><div><h2>All categories</h2><p>{{ $categories->total() }} sections</p></div></div><div class="table-responsive"><table class="table admin-table align-middle mb-0"><thead><tr><th>Category</th><th>Parents</th><th>Stories</th><th>Slug</th><th></th></tr></thead><tbody>
        @forelse($categories as $category)
            <tr><td><strong>{{ $category->name }}</strong><small>{{ $category->description }}</small></td><td>{{ $category->parents->pluck('name')->implode(', ') ?: '—' }}</td><td>{{ $category->articles_count }}</td><td><code>{{ $category->slug }}</code></td><td class="text-end text-nowrap"><button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#edit-category-{{ $category->id }}" aria-label="Edit {{ $category->name }}"><i class="bi bi-pencil"></i></button> <form class="d-inline" method="POST" action="{{ route('admin.categories.destroy',$category) }}" data-confirm="Delete this category?">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger" aria-label="Delete {{ $category->name }}"><i class="bi bi-trash"></i></button></form></td></tr>
            <tr class="collapse" id="edit-category-{{ $category->id }}"><td colspan="5"><form method="POST" action="{{ route('admin.categories.update',$category) }}" class="category-edit-form">@csrf @method('PUT')
                <div><label class="form-label">Name</label><input class="form-control form-control-sm" name="name" value="{{ $category->name }}" maxlength="80" required></div>
                <div><label class="form-label">Slug</label><input class="form-control form-control-sm category-slug-preview" value="{{ $category->slug }}" readonly></div>
                <div><label class="form-label">Parent categories</label><select class="form-select form-select-sm category-parent-select" name="parent_ids[]" multiple>@foreach($allCategories as $parent)@if($parent->id !== $category->id)<option value="{{ $parent->id }}" @selected($category->parents->contains('id', $parent->id))>{{ $parent->name }}</option>@endif @endforeach</select></div>
                <div><label class="form-label">Description</label><input class="form-control form-control-sm" name="description" value="{{ $category->description }}" maxlength="240"></div>
                <div class="category-edit-actions"><button class="btn btn-dark btn-sm">Save category</button></div>
            </form></td></tr>
        @empty<tr><td colspan="5" class="text-center py-5">No categories found.</td></tr>@endforelse
    </tbody></table></div><div class="admin-pagination">{{ $categories->links('pagination::bootstrap-5') }}</div></div></div>
</div>
@endsection
