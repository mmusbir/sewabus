@php
    $editing = isset($userAccount);
@endphp

<div class="space-y-6">
    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $editing ? $userAccount->name : '') }}" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
        @error('name') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Email</label>
        <input type="email" name="email" value="{{ old('email', $editing ? $userAccount->email : '') }}" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
        @error('email') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Role</label>
        <select name="role" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @foreach($roles as $roleKey => $roleLabel)
                <option value="{{ $roleKey }}" {{ old('role', $editing ? $userAccount->role : \App\Models\User::ROLE_USER) === $roleKey ? 'selected' : '' }}>
                    {{ $roleLabel }}
                </option>
            @endforeach
        </select>
        @error('role') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
        <div class="text-xs text-slate-500 leading-relaxed rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-3">
            <p><span class="font-semibold">Role & Hak Akses:</span></p>
            <p>Super Admin: semua fitur + manajemen akun pengguna.</p>
            <p>Admin: dashboard, galeri, paket, dan pengaturan umum/SEO.</p>
            <p>Editor: dashboard, galeri, dan paket (tanpa menu pengaturan).</p>
            <p>User: akun biasa, tanpa akses panel admin.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">
                {{ $editing ? 'Password Baru (Opsional)' : 'Password' }}
            </label>
            <input type="password" name="password" {{ $editing ? '' : 'required' }} class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('password') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">
                {{ $editing ? 'Konfirmasi Password Baru' : 'Konfirmasi Password' }}
            </label>
            <input type="password" name="password_confirmation" {{ $editing ? '' : 'required' }} class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
        </div>
    </div>
</div>
