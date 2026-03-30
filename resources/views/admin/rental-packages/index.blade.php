@extends('layouts.admin')

@section('title', 'Paket Sewa / Liburan - Panel Admin')
@section('header_title', 'Paket Sewa / Liburan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h3 class="text-lg font-bold">Daftar Paket</h3>
    <a href="{{ route('admin.rental-packages.create') }}" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-lg shadow-primary/20">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Paket
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-slate-200 dark:border-slate-700">
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Gambar</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Nama Paket</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Jenis</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Harga</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Status</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500">Urutan</th>
                <th class="py-3 px-4 font-bold text-sm text-slate-500 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($packages as $package)
                <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="py-3 px-4">
                        @if($package->image_path)
                            <img src="{{ $package->image_path }}" alt="{{ $package->title }}" class="h-16 w-24 object-cover rounded-lg border border-slate-200">
                        @else
                            <div class="h-16 w-24 rounded-lg border border-dashed border-slate-300 flex items-center justify-center text-slate-400 text-xs">
                                No Image
                            </div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <p class="font-bold text-sm">{{ $package->title }}</p>
                        <p class="text-xs text-slate-500">{{ $package->duration ?: '-' }}</p>
                    </td>
                    <td class="py-3 px-4">
                        <span class="{{ $package->type === 'liburan' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                            {{ $package->type === 'liburan' ? 'Liburan' : 'Sewa' }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm">{{ $package->price_label ?: '-' }}</td>
                    <td class="py-3 px-4">
                        <span class="{{ $package->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }} px-3 py-1 rounded-full text-xs font-bold">
                            {{ $package->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm">{{ $package->sort_order }}</td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.rental-packages.edit', $package) }}" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </a>
                            <form action="{{ route('admin.rental-packages.destroy', $package) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket ini?');">
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
                    <td colspan="7" class="py-8 text-center text-slate-500 text-sm">Belum ada paket yang ditambahkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
