@props(['align' => 'end', 'width' => '48', 'contentClasses' => ''])
<div class="dropdown">
    <div data-bs-toggle="dropdown" aria-expanded="false">{{ $trigger }}</div>
    <div class="dropdown-menu dropdown-menu-{{ $align }} {{ $contentClasses }}" style="min-width:{{ $width === '48' ? '12rem' : $width }}">{{ $content }}</div>
</div>
