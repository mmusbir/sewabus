@php
    $coverImage = $gallery->image_path ?: ($gallery->images->first()->media_path ?? '/stitch_img_bus_shd.jpg');
@endphp

<div class="group bg-white dark:bg-slate-900/50 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 hover:shadow-xl transition-all">
    <div class="relative aspect-[16/10] overflow-hidden">
        <div class="absolute top-3 inset-x-3 z-10 flex items-start justify-between gap-2">
            <span @class([
                'text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider',
                gallery_category_badge_class($gallery->category),
            ])>
                {{ gallery_category_label($gallery->category, $gallery->category) }}
            </span>
            @if(filled($gallery->po_key))
                <span
                    class="text-[10px] font-bold px-3 py-1 rounded-full tracking-wide shadow-lg shadow-slate-950/10"
                    style="{{ gallery_po_badge_style($gallery->po_key) }}"
                >
                    PO {{ gallery_po_label($gallery->po_key, $gallery->po_key) }}
                </span>
            @endif
        </div>
        <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-110" data-alt="{{ $gallery->title }}" style="background-image: url('{{ $coverImage }}')"></div>
    </div>
    <div class="p-5">
        <h3 class="text-lg font-bold mb-1 text-slate-900 dark:text-slate-100">{{ $gallery->title }}</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">{{ $gallery->description ?? 'Deskripsi tidak tersedia.' }}</p>
        <a href="{{ route('katalog.show', $gallery) }}" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
            Lihat Detail
            <x-fa-icon name="arrow-right" class="fa-fw text-sm" />
        </a>
    </div>
</div>
