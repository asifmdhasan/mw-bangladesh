@props(['name', 'show' => false, 'maxWidth' => 'lg'])
<div class="modal fade" id="{{ $name }}" tabindex="-1" aria-labelledby="{{ $name }}-title" aria-hidden="true" data-auto-show="{{ $show ? 'true' : 'false' }}">
    <div class="modal-dialog modal-dialog-centered {{ in_array($maxWidth,['sm','lg','xl'],true) ? 'modal-'.$maxWidth : '' }}"><div class="modal-content">{{ $slot }}</div></div>
</div>
