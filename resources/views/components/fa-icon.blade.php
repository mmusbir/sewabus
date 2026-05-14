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

    $key = $style === 'brands' ? 'brands:'.$name : $name;
    $icons = [
        'arrow-left' => '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
        'arrow-right' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'bars' => '<path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/>',
        'bus' => '<path d="M6 17h12"/><path d="M6 17v2"/><path d="M18 17v2"/><path d="M5 6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v9c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2V6Z"/><path d="M7 9h10"/><path d="M8 14h.01"/><path d="M16 14h.01"/>',
        'circle-check' => '<path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path d="m8 12 3 3 5-6"/>',
        'circle-play' => '<path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path d="m10 8 6 4-6 4V8Z"/>',
        'clock' => '<path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path d="M12 7v5l3 2"/>',
        'envelope' => '<path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/>',
        'filter' => '<path d="M4 6h16"/><path d="M7 12h10"/><path d="M10 18h4"/>',
        'headset' => '<path d="M4 13a8 8 0 0 1 16 0"/><path d="M4 13v3a2 2 0 0 0 2 2h1v-7H6a2 2 0 0 0-2 2Z"/><path d="M20 13v3a2 2 0 0 1-2 2h-1v-7h1a2 2 0 0 1 2 2Z"/><path d="M17 18c0 2-2 3-5 3"/>',
        'id-badge' => '<path d="M8 3h8"/><path d="M9 3v3"/><path d="M15 3v3"/><path d="M6 6h12v15H6z"/><path d="M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0Z"/><path d="M8 19c1-2 7-2 8 0"/>',
        'list-check' => '<path d="m3 7 2 2 4-4"/><path d="M13 7h8"/><path d="m3 17 2 2 4-4"/><path d="M13 17h8"/>',
        'location-dot' => '<path d="M12 21s7-5.2 7-12a7 7 0 1 0-14 0c0 6.8 7 12 7 12Z"/><path d="M12 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>',
        'moon' => '<path d="M20 15.5A8 8 0 0 1 8.5 4 8 8 0 1 0 20 15.5Z"/>',
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.7 19.7 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.7 19.7 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L8.1 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7A2 2 0 0 1 22 16.9Z"/>',
        'shield-halved' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="M12 2v20"/>',
        'sliders' => '<path d="M4 6h10"/><path d="M18 6h2"/><path d="M16 4v4"/><path d="M4 12h2"/><path d="M10 12h10"/><path d="M8 10v4"/><path d="M4 18h12"/><path d="M20 18h0"/><path d="M18 16v4"/>',
        'sun' => '<path d="M12 4V2"/><path d="M12 22v-2"/><path d="m4.9 4.9 1.4 1.4"/><path d="m17.7 17.7 1.4 1.4"/><path d="M4 12H2"/><path d="M22 12h-2"/><path d="m4.9 19.1 1.4-1.4"/><path d="m17.7 6.3 1.4-1.4"/><path d="M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/>',
        'wallet' => '<path d="M4 7h15a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h13"/><path d="M16 13h5"/><path d="M16 13a2 2 0 1 0 0 4h5v-4h-5Z"/>',
        'xmark' => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
        'brands:facebook-f' => '<path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v6h4v-6h3l1-4h-4V9c0-.6.4-1 1-1Z"/>',
        'brands:instagram' => '<path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Z"/><path d="M16 11.4A4 4 0 1 1 12.6 8 4 4 0 0 1 16 11.4Z"/><path d="M17.5 6.5h.01"/>',
        'brands:tiktok' => '<path d="M14 3v10.5a4.5 4.5 0 1 1-4.5-4.5"/><path d="M14 3c.5 3 2.5 5 5 5"/>',
        'brands:whatsapp' => '<path d="M20 11.5a8 8 0 0 1-11.7 7.1L4 20l1.4-4.1A8 8 0 1 1 20 11.5Z"/><path d="M9 8c.5 4 3 6 7 7"/><path d="M9 8h2l1 2-1 1"/><path d="M14 14l1-1 2 1v2"/>',
    ];

    $svg = $icons[$key] ?? null;
@endphp

@if ($svg)
    <svg
        {{ $attributes->class(['inline-block', 'h-[1em]', 'w-[1em]', 'shrink-0', 'align-[-0.125em]'])->merge([
            'viewBox' => '0 0 24 24',
            'fill' => 'none',
            'stroke' => 'currentColor',
            'stroke-width' => '2',
            'stroke-linecap' => 'round',
            'stroke-linejoin' => 'round',
            'aria-hidden' => 'true',
            'focusable' => 'false',
        ]) }}
    >{!! $svg !!}</svg>
@else
    <i {{ $attributes->class([$prefix, 'fa-'.$name]) }} aria-hidden="true"></i>
@endif
