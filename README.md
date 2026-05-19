# Realtime Chat Application

Aplikasi web real-time chat interaktif dengan fitur private chat, group chat, dan tracking status online/offline pengguna secara realtime.

## Fitur Utama
- Login & Register
- Dashboard daftar pengguna
- Private chat & Group chat realtime
- Status online/offline realtime
- Riwayat pesan tersimpan di database

## Teknologi yang Digunakan

### Backend
- PHP 8+
- Laravel 12
- Laravel Breeze 
- Laravel Reverb 
- MySQL

### Frontend
- Laravel Blade 
- Tailwind CSS
- Vanilla JavaScript & Alpine.js
- Laravel Echo
- Pusher JS

## Cara Install

### 1. Clone Repository
```bash
git clone https://github.com/juprihaikal/realtime-chat
cd realtime-chat
```

### 2. Install Dependency
```bash
composer install
npm install
```

### 3. Copy File .env
```bash
copy .env.example .env
```

### 4. Generate Key
```bash
php artisan key:generate
```

### 5. Buat Database
Buat database di MySQL/phpMyAdmin dengan nama:
`realtime-chat`

Pastikan settingan database di file `.env` lu udah sesuai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realtime-chat
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Jalankan Migration
```bash
php artisan migrate
```

*(Catatan: Package Reverb, Echo, dan Pusher-JS sudah otomatis terinstall lewat perintah `composer install` dan `npm install` di atas, jadi tidak perlu install ulang)*

## Menjalankan Aplikasi

Jalankan 3 terminal secara bersamaan untuk menjalankan aplikasi:

**Terminal 1 (Menjalankan server Laravel):**
```bash
php artisan serve
```

**Terminal 2 (Menjalankan websocket server Reverb):**
```bash
php artisan reverb:start
```

**Terminal 3 (Menjalankan Vite asset bundler):**
```bash
npm run dev
```

