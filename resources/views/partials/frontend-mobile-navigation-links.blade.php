@foreach($categories as $category)
    <div class="mobile-drawer-item {{ $category->children->isNotEmpty() ? 'has-children' : '' }}">
        <div class="mobile-drawer-item-row">
            <a class="mobile-drawer-link {{ isset($activeParentCategory) && $activeParentCategory->is($category) ? 'active' : '' }}" href="{{ $category->url }}">{{ $category->name }}</a>
            @if($category->children->isNotEmpty())
                <button class="mobile-drawer-submenu-toggle" type="button" aria-label="Show {{ $category->name }} submenu" aria-expanded="false"><span aria-hidden="true"></span></button>
            @endif
        </div>
        @if($category->children->isNotEmpty())
            <div class="mobile-drawer-submenu" aria-hidden="true" inert>
                @foreach($category->children as $child)
                    <a class="mobile-drawer-sublink {{ isset($activeParentCategory) && $activeParentCategory->is($category) && request()->url() === $child->url ? 'active' : '' }}" href="{{ $child->url }}">{{ $child->name }}</a>
                @endforeach
            </div>
        @endif
    </div>
@endforeach
