# Collaborative Docs — Google Docs Clone

Editor dokumen kolaboratif *realtime* yang dibangun dengan Laravel 12. Sistem ini memungkinkan banyak pengguna untuk mengedit dokumen yang sama secara bersamaan dengan fitur pelacakan kursor, riwayat versi, dan penanganan konflik.

## Fitur Utama

- **Realtime Editing**: Sinkronisasi konten instan menggunakan Laravel Reverb.
- **Multi-user Support**: Mendukung kolaborasi antar pengguna dalam satu dokumen.
- **Live Cursor Tracking**: Melihat posisi kursor pengguna lain secara langsung.
- **Version History**: Menyimpan dan memulihkan riwayat perubahan dokumen.
- **Conflict Resolution**: Sistem log untuk mencatat siapa yang mengedit apa dan menangani potensi konflik data.
- **Rich Text Toolbar**: Mendukung format teks dasar (Bold, Italic, dll).

## Arsitektur Sistem

- **Sync**: Menggunakan *debounce* (450ms) untuk mengurangi beban server.
- **Cursor**: Menggunakan *throttle* (120ms) untuk efisiensi data.
- **Broadcasting**: Menggunakan Laravel Reverb (WebSocket) untuk komunikasi dua arah.

## Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js 18 atau lebih tinggi
- SQLite3 (sebagai database utama)

## Instalasi

1. **Clone repository:**
   ```bash
   git clone [URL_REPO_ANDA]
   cd collaborative-docs

2. **Install dependensi:**
   ```Bash
   composer install
   npm install
  
3. **Konfigurasi Environment:**
   ```Bash
    cp .env.example .env
    php artisan key:generate
Pastikan pengaturan .env Anda sudah benar:
```Cuplikan kode
   DB_CONNECTION=sqlite
   BROADCAST_CONNECTION=reverb
   ```
4. **Database:**
   ```Bash
   php artisan migrate


Menjalankan Aplikasi
Untuk menjalankan aplikasi, Anda memerlukan tiga proses yang berjalan secara bersamaan:
Bash
# Terminal 1: Laravel Server
    ```php artisan serve    
# Terminal 2: WebSocket Server
    ```php artisan reverb:start
# Terminal 3: Vite Dev Server
    ```npm run dev

Akses aplikasi di: http://127.0.0.1:8000
Cara Penggunaan
1. Registrasi:Buka aplikasi dan buat akun baru melalui halaman Register.
2. Login: Masuk ke akun Anda.
3. Kolaborasi: Buat dokumen baru. Bagikan link dokumen kepada pengguna lain. Anda dan pengguna lain dapat mengetik di dokumen yang sama dan perubahan akan muncul secara realtime.
4. Log Aktivitas: Lihat riwayat perubahan dokumen melalui menu Activity Log atau History
# Struktur Proyek
KategoriDeskripsiModelsDocument, DocumentRevision, DocumentEdit, UserEventsDocumentContentUpdated, CursorMovedControllersAuthController, DocumentController, RevisionControllerFrontendeditor.js (Logika sync) & editor.css