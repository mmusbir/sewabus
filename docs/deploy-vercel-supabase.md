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

## 3. Deploy ke Vercel

```bash
npm i -g vercel
vercel login
vercel
```

Saat project sudah terhubung, deploy production:

```bash
vercel --prod
```

## 4. Jalankan Migrasi ke Supabase

Setelah env production terisi, jalankan migrasi dari mesin lokal:

```bash
php artisan migrate --force
```

Atau arahkan `.env` lokal sementara ke Supabase menggunakan template [supabase.env.example](/d:/Laragon/www/sewabus/supabase.env.example).

## 5. Catatan Penting

- Vercel memakai filesystem ephemeral. Karena itu upload media tidak boleh lagi bergantung pada `storage/app/public` lokal.
- Kode project ini sekarang menyimpan path file secara storage-agnostic agar bisa pindah ke disk `s3`.
- Vercel Functions punya batas ukuran request. Upload video besar atau multi-upload besar dari panel admin masih bisa mentok limit platform. Untuk kebutuhan upload besar, solusi yang benar adalah direct upload browser ke storage.
