# Deploy Laravel ke Vercel + Supabase

## Ringkasan

Project ini sudah disiapkan untuk:

- deploy ke Vercel lewat `api/index.php` + `vercel.json`
- konek ke Supabase Postgres lewat `DB_URL`
- simpan upload media ke storage S3-compatible lewat `MEDIA_DISK=s3`

## 1. Siapkan Supabase

Di dashboard Supabase:

1. Buka project Anda.
2. Ambil connection string dari menu `Connect`.
3. Untuk Laravel, paling aman mulai dari `Session Pooler` port `5432`.
4. Buat bucket public untuk file upload, misalnya `public`.
5. Jika ingin memakai Supabase Storage lewat driver `s3`, ambil access key dan secret key storage dari dashboard/project settings yang sesuai.

## 2. Isi Environment Variables di Vercel

Gunakan nilai dari [supabase.env.example](/d:/Laragon/www/sewabus/supabase.env.example) sebagai template.

Minimal yang wajib:

- `APP_NAME`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY`
- `APP_URL=https://nama-project.vercel.app`
- `DB_CONNECTION=pgsql`
- `DB_URL=postgres://...`
- `DB_SSLMODE=require`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=sync`
- `LOG_CHANNEL=stderr`

Untuk upload media di production:

- `MEDIA_DISK=s3`
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_DEFAULT_REGION`
- `AWS_BUCKET`
- `AWS_ENDPOINT`
- `AWS_URL`
- `AWS_USE_PATH_STYLE_ENDPOINT=true`

## 3. Konfigurasi vercel.json

File `vercel.json` sudah dikonfigurasi dengan:

```json
{
  "buildCommand": "composer install && php artisan config:clear && php artisan cache:clear"
}
```

**Penting**: Build command ini membersihkan cache configuration setiap deploy. Karena Vercel menggunakan filesystem ephemeral, config cache harus selalu fresh.\

## 4. Deploy ke Vercel

```bash
npm i -g vercel
vercel login
vercel
```

Saat project sudah terhubung, deploy production:

```bash
vercel --prod
```

## 5. Jalankan Migrasi ke Supabase

Setelah env production terisi di Vercel, jalankan migrasi dari mesin lokal menggunakan .env sementara:

### Opsi A: Edit .env lokal sementara (TIDAK MEREKAM DI GIT)

1. **Backup .env lokal asli**:
```bash
cp .env .env.local-backup
```

2. **Salin credentials dari `.vercel.production.env` ke `.env` lokal sementara**:
   - Gunakan `DB_URL` dari production
   - Gunakan `DB_SSLMODE=require`

3. **Jalankan migrasi**:
```bash
php artisan migrate --force
```

4. **Restore .env lokal asli**:
```bash
mv .env.local-backup .env
```

### Opsi B: Jalankan dengan flag

```bash
DB_URL="postgresql://postgres.xxx@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres" \
DB_SSLMODE=require \
DB_CONNECTION=pgsql \
php artisan migrate --force
```

⚠️ **PENTING**: Jangan commit credentials production ke Git!

## 6. Catatan Penting

- Vercel memakai filesystem ephemeral. Karena itu upload media tidak boleh lagi bergantung pada `storage/app/public` lokal.
- Kode project ini sekarang menyimpan path file secara storage-agnostic agar bisa pindah ke disk `s3`.
- Vercel Functions punya batas ukuran request. Upload video besar atau multi-upload besar dari panel admin masih bisa mentok limit platform. Untuk kebutuhan upload besar, solusi yang benar adalah direct upload browser ke storage.

## 7. Troubleshoot HTTP 500 di Production

### Error: "Target class [view] does not exist"

Ini adalah configuration cache yang tidak valid di Vercel. Solusi:

1. **Pastikan `vercel.json` punya build command**:
   ```json
   {
     "buildCommand": "composer install && php artisan config:clear && php artisan cache:clear"
   }
   ```

2. **Trigger re-deploy**:
   ```bash
   git add vercel.json
   git commit -m "Fix: ensure config cache is cleared on deploy"
   git push
   ```
   
   Vercel akan otomatis re-deploy dan menjalankan build command yang benar.

3. **Jika tetap error**, bersihkan Vercel cache:
   - Dashboard Vercel > Settings > Git
   - Klik "Purge Deployment Cache"
   - Trigger re-deploy dengan `git push`

### Checklist Umum:

1. **Vercel environment variable sudah disinkronisasi?**
   - Buka dashboard Vercel > Settings > Environment Variables
   - Pastikan semua variable dari `supabase.env.example` sudah ada
   - Khususnya: `DB_URL`, `DB_SSLMODE=require`, `SESSION_DRIVER=database`

2. **Database migrations sudah dijalankan?**
   ```bash
   # Cek dari lokal apakah migrations sudah done di Supabase:
   php artisan migrate:status --force
   ```
   - Jika semua belum dijalankan, ikuti langkah **4. Jalankan Migrasi ke Supabase**

3. **Lihat error log di Vercel**:
   - Dashboard Vercel > Deployments > pilih deployment > Logs (tab Runtime Logs)
   - Cari error message detail tentang database atau sessions table

4. **Cek koneksi Supabase**:
   - Sslmode harus `require` untuk Supabase (bukan `disable` atau `prefer`)
   - Gunakan pooler connection string untuk aplikasi web (port 5432 atau 6543)

5. **Verifikasi Sessions Table**:
   ```bash
   php artisan session:table
   php artisan migrate --force
   ```

Jika masih error, cek Vercel logs dan screenshot error message, lalu trace ulang langkah 1-4 di atas.
