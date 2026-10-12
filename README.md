# Dishub Uji Berkala

Aplikasi pendataan uji berkala kendaraan dengan dashboard, formulir pemeriksaan, dan pengelolaan berkas.

**Status:** Pengembangan lokal; demo publik belum tersedia.

## Fitur

- Autentikasi dan dashboard petugas
- Pencatatan hasil uji serta masa berlaku
- Unggahan dokumen kendaraan dan tampilan hasil

## Teknologi

PHP, MySQL, HTML, CSS, JavaScript.

## Menjalankan secara lokal

Gunakan server Apache/PHP, misalnya XAMPP. Tempatkan salinan proyek pada document root dan buka `login.php` melalui localhost. Aturan `.htaccess`, jika tersedia, membutuhkan Apache dengan konfigurasi yang sesuai.

## Database

SQL siap impor tersedia di [`database/dishub.sql`](database/dishub.sql). File ini berasal dari dump proyek yang telah dibersihkan: struktur tabel, indeks, dan relasi dipertahankan; akun, hash password, data pribadi, serta data operasional asli tidak disertakan.

1. Buat database kosong bernama `dishub` melalui phpMyAdmin.
2. Pilih database tersebut, buka tab **Import**, lalu pilih `database/dishub.sql`. SQL ditujukan untuk database kosong dan tidak menghapus tabel yang sudah ada.
3. Salin template konfigurasi sesuai tabel di bawah, lalu isi host, nama database `dishub`, username, dan password MySQL lokal Anda pada `config.php`.
4. Dari folder proyek, jalankan `php database/create-admin.php` untuk membuat akun lokal dengan password pilihan Anda. Tidak ada akun atau password bawaan di repository.

Alternatif impor melalui terminal MySQL, setelah database kosong dibuat:

```sql
USE dishub;
SOURCE database/dishub.sql;
```

Jalankan perintah `SOURCE` dari folder proyek. Pada XAMPP Windows, PHP CLI tersedia di `C:\xampp\php\php.exe`.


Endpoint `save_uji.php` menggunakan environment server `DB_DSN`, `DB_USERNAME`, dan `DB_PASSWORD`. Isi `DB_DSN` dengan DSN MySQL untuk database pengembangan yang sama dengan `config.php`.

## Konfigurasi privat

Salin template berikut ke nama file lokalnya, lalu isi nilai untuk lingkungan Anda:

| Template | File lokal |
| --- | --- |
| `config.example.php` | `config.php` |

File konfigurasi lokal diabaikan oleh Git. Jangan memasukkan password, API key, atau data akun ke source code.

## Catatan repository

Dependensi terpasang, cache, file `.env`, data database operasional, dan unggahan pengguna tidak disertakan. Nama merek dan aset pihak ketiga tetap mengikuti hak pemiliknya. Tidak ada lisensi penggunaan ulang yang diberikan melalui repository ini.
