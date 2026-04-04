@props([
    'name',
    'style' => 'solid',
])

@php
    $prefix = match ($style) {
        'brands' => 'fa-brands',
        'regular' => 'fa-regular',
        default => 'fa-solid',
    };
@endphp

<i {{ $attributes->class([$prefix, 'fa-'.$name]) }} aria-hidden="true"></i>
