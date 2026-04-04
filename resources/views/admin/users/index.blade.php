@extends('layouts.admin')

@section('title', 'Manajemen Akun - Panel Admin')
@section('header_title', 'Manajemen Akun Pengguna')

@section('content')
<div class="space-y-6">
    @if($errors->any())
        <div class="p-4 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold">Daftar Akun Pengguna</h3>
            <p class="text-xs text-slate-500 mt-1">Kelola akun, role, dan reset password sesuai hak akses.</p>
        </div>
        <a href="{{ route('admin.settings.users.create') }}" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors shadow-lg shadow-primary/20">
            <x-fa-icon name="user-plus" class="fa-fw text-sm" />
            Tambah Akun
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700">
                    <th class="py-3 px-4 font-bold text-sm text-slate-500">Nama</th>
                    <th class="py-3 px-4 font-bold text-sm text-slate-500">Email</th>
                    <th class="py-3 px-4 font-bold text-sm text-slate-500">Role</th>
                    <th class="py-3 px-4 font-bold text-sm text-slate-500">Dibuat</th>
                    <th class="py-3 px-4 font-bold text-sm text-slate-500 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $roleBadge = match($user->role) {
                            \App\Models\User::ROLE_SUPER_ADMIN => 'bg-rose-100 text-rose-700',
                            \App\Models\User::ROLE_ADMIN => 'bg-emerald-100 text-emerald-700',
                            \App\Models\User::ROLE_EDITOR => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="py-3 px-4 font-bold text-sm">{{ $user->name }}</td>
                        <td class="py-3 px-4 text-sm">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            <span class="{{ $roleBadge }} px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                {{ $user->roleLabel() }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-500">{{ $user->created_at?->format('d M Y H:i') }}</td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.settings.users.edit', $user) }}" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg transition-colors" title="Edit">
                                    <x-fa-icon name="pen-to-square" class="fa-fw text-sm" />
                                </a>

                                <form action="{{ route('admin.settings.users.reset-password', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Reset password akun ini? Password baru akan ditampilkan sekali.');">
                                    @csrf
                                    <button type="submit" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Reset Password">
                                        <x-fa-icon name="key" class="fa-fw text-sm" />
                                    </button>
                                </form>

                                <form action="{{ route('admin.settings.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus akun ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <x-fa-icon name="trash" class="fa-fw text-sm" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-500 text-sm">Belum ada akun pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
