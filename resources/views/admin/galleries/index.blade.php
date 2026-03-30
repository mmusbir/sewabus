@extends('layouts.admin')

@section('title', 'Galeri Armada - Panel Admin')
@section('header_title', 'Galeri Armada')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h3 class="text-lg font-bold">Daftar Armada</h3>
    <a href="{{ route('admin.galleries.create') }}" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Armada
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-slate-200 dark:border-slate-700">
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Gambar</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Nama Armada</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Kategori</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Jumlah Unit</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Status</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($galleries as $gallery)
                <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="py-3 px-4">
                        <img src="{{ $gallery->image_path }}" alt="{{ $gallery->title }}" class="h-16 w-24 object-cover rounded-lg border border-slate-200">
                    </td>
                    <td class="py-3 px-4 font-bold text-sm">{{ $gallery->title }}</td>
                    <td class="py-3 px-4">
                        <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                            {{ ucfirst($gallery->category) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm font-semibold text-slate-700 dark:text-slate-200">
                        {{ $gallery->unit_count ?? 1 }} unit
                    </td>
                    <td class="py-3 px-4">
                        <form action="{{ route('admin.galleries.toggle-status', $gallery) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $gallery->is_active ? 0 : 1 }}">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $gallery->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-200' }}">
                                {{ $gallery->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                            <button type="submit" class="text-xs font-semibold px-2 py-1 rounded-md border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                {{ $gallery->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.galleries.edit', $gallery) }}" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus armada ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500 text-sm">Belum ada armada yang ditambahkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
