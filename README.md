# Sistem Informasi Infak Sekolah

Versi: **v1.0**

Sistem Informasi Infak Sekolah adalah aplikasi Laravel untuk mengelola komitmen infak, tagihan, pembayaran, validasi bukti bayar, data siswa, riwayat akademik, rayon, rombel, guru pembimbing, dan laporan infak sekolah.

## Ringkasan Fitur

- Dashboard admin dengan statistik siswa, rayon, tagihan, pembayaran valid, antrian validasi, tren pembayaran, dan status tagihan.
- Hak akses berbasis role:
  - Admin Utama
  - Petugas Infak
  - Siswa/Keluarga
  - Pembimbing Rayon/Guru
- Master data tahun ajaran, guru, rayon, rombel, siswa, user dan role.
- Import XLSX dan template XLSX untuk master data dan komitmen infak.
- Riwayat akademik siswa dari kelas X, XI, XII tetap tersimpan walaupun rombel berubah.
- Komitmen infak per siswa dan per tahun ajaran.
- Generate tagihan infak bulanan.
- Pelaporan pembayaran oleh siswa/keluarga dengan upload bukti bayar.
- Validasi pembayaran oleh admin atau petugas infak.
- Pembimbing rayon hanya melihat siswa sesuai rayon bimbingannya.
- Menu laporan admin untuk rekap kepala sekolah: ringkasan tagihan, pembayaran, tunggakan, rekap rayon, status tagihan, dan daftar tagihan belum lunas.
- Footer credit aplikasi mengarah ke Instagram pengembang.

## Keamanan

Aplikasi sudah dilengkapi beberapa hardening dasar:

- Role middleware untuk membatasi akses menu dan route.
- Public registration dinonaktifkan; akun dibuat oleh admin.
- Password wajib minimal 8 karakter, huruf kapital, huruf kecil, dan karakter khusus.
- Checklist password interaktif pada form pembuatan/perubahan password.
- CSRF protection Laravel pada form.
- Validasi upload bukti bayar hanya untuk `jpg`, `jpeg`, `png`, dan `pdf`.
- Security headers:
  - `X-Frame-Options`
  - `X-Content-Type-Options`
  - `Referrer-Policy`
  - `Permissions-Policy`
  - `Cross-Origin-Opener-Policy`
  - `Cross-Origin-Resource-Policy`
  - `Content-Security-Policy`
  - `Strict-Transport-Security` saat HTTPS aktif
- `.env.example` memakai `APP_DEBUG=false`.

## Akun Dummy

Seeder menyediakan 1 akun dummy untuk setiap role.

```text
admin@infak.test       / Infak#2026
petugas@infak.test     / Infak#2026
pembimbing@infak.test  / Infak#2026
siswa@infak.test       / Infak#2026
```

## Instalasi Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
php artisan serve
```

Sesuaikan konfigurasi database di `.env` sebelum menjalankan migrasi.

## Pengujian

```bash
vendor/bin/pint --test
php artisan route:list
php artisan view:cache
php artisan test
npm audit --omit=dev
```

## Versi Aplikasi

Versi aplikasi disimpan di file:

```text
VERSION
```

Versi awal saat ini adalah:

```text
1.0
```

Untuk menaikkan versi saat ada rilis perubahan besar, gunakan command berikut:

```bash
php artisan app:version-bump major
php artisan app:version-bump minor
php artisan app:version-bump patch
```

Aturan rilis yang disarankan:

- `major`: perubahan besar atau breaking change.
- `minor`: fitur besar baru yang kompatibel.
- `patch`: perbaikan bug, security fix, atau perubahan kecil.

Tampilan aplikasi akan membaca versi dari file `VERSION` dan menampilkannya sebagai `vX.Y` atau `vX.Y.Z`.

## Teknologi

- Laravel
- PHP
- MySQL/MariaDB
- Bootstrap
- Bootstrap Icons
- Vite

## Pengembang

Dikembangkan oleh [Ardi Ariansyah](https://www.instagram.com/ardiariansyah07).
