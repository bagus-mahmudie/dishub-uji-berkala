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

Aplikasi membutuhkan MySQL. Dump database dan data operasional tidak disertakan; siapkan skema database yang sesuai sebelum menjalankan fitur dinamis. Repository ini belum menyediakan instalasi database otomatis.

Endpoint `save_uji.php` menggunakan environment server `DB_DSN`, `DB_USERNAME`, dan `DB_PASSWORD`. Isi `DB_DSN` dengan DSN MySQL untuk database pengembangan yang sama dengan `config.php`.

## Konfigurasi privat

Salin template berikut ke nama file lokalnya, lalu isi nilai untuk lingkungan Anda:

| Template | File lokal |
| --- | --- |
| `config.example.php` | `config.php` |

File konfigurasi lokal diabaikan oleh Git. Jangan memasukkan password, API key, atau data akun ke source code.

## Catatan repository

Dependensi terpasang, cache, file `.env`, database, dan unggahan pengguna tidak disertakan. Nama merek dan aset pihak ketiga tetap mengikuti hak pemiliknya. Tidak ada lisensi penggunaan ulang yang diberikan melalui repository ini.
