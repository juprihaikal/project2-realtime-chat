# RMD Connect - Realtime Chat Application

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
- Laravel Reverb (Websocket Server)
- MySQL

### Frontend
- Tailwind CSS (menggantikan Bootstrap)
- Vanilla JavaScript & Alpine.js
- Laravel Echo
- Pusher JS

## Cara Install

### 1. Clone Repository
```bash
git clone https://github.com/ramanda456/rmd_connect.git
cd rmd_connect
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
Buat database bernama:
`rmd_connect`

Lalu atur koneksi database di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rmd_connect
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Jalankan Migration
```bash
php artisan migrate
```

### 7. Install Broadcasting (Opsional, jika belum terinstall)
```bash
php artisan install:broadcasting
```
Pilih: `reverb`

### 8. Install Echo & Pusher (Opsional, jika belum ada di package.json)
```bash
npm install --save-dev laravel-echo pusher-js
```

## Menjalankan Aplikasi

Jalankan 3 terminal secara bersamaan untuk menjalankan aplikasi secara penuh:

**Terminal 1 (Menjalankan server PHP):**
```bash
php artisan serve
```

**Terminal 2 (Menjalankan websocket server Reverb):**
```bash
php artisan reverb:start
```

**Terminal 3 (Menjalankan asset bundler):**
```bash
npm run dev
```

Buka browser dan akses: `http://localhost:8000`
