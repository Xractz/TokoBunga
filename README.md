# TokoBunga - Bloomify

Proyek E-Commerce Toko Bunga dengan fitur lengkap mulai dari katalog, keranjang belanja, checkout, hingga manajemen pesanan.

## 🚀 Instalasi & Persiapan

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

### 1. Clone Repository

Pertama, clone repository ini ke direktori lokal Anda:

```bash
git clone https://github.com/Xractz/TokoBunga.git
cd TokoBunga
```

### 2. Konfigurasi (.env)

Salin, ubah nama file `.env.example` menjadi `.env`, dan sesuaikan konfigurasinya:

```bash
cp .env.example .env
```

Buka file `.env` dan atur konfigurasi berikut (sesuaikan dengan environment Anda):

```ini
APP_URL=http://tokobunga.test #sesuai dengan localserver

# Konfigurasi Database
DB_HOST=localhost
DB_NAME=tokobunga
DB_USER=root
DB_PASSWORD=

# SMTP CONFIGURATION
SMTP_HOST=mail.bloomify.biz.id
SMTP_USER=no-reply@bloomify.biz.id
SMTP_PASS=
SMTP_PORT=465

# PAYMENT GATEWAT CONFIGURATION
PAKASIR_SLUG=
PAKASIR_API=

PAKASIR_WEBHOOK_SECRET=
```

### 3. Import Database

Import file database yang telah disediakan ke dalam MySQL:

1. Buat database baru bernama `tokobunga` (sesuai `DB_NAME` di .env).
2. Import file SQL yang terletak di:
   `database/tokobunga.sql`

## 🔐 Akun Login Demo

Gunakan kredensial berikut untuk masuk ke dalam aplikasi:

**Administrator (Panel Admin)**

- **Email:** `admin@gmail.com`
- **Password:** `123456`

**Customer (Pelanggan)**

- **Email:** `cust@gmail.com`
- **Password:** `123456`

---

### Catatan Tambahan

Pastikan server lokal (seperti XAMPP, Laragon, atau built-in PHP server) telah berjalan dan diarahkan ke folder proyek ini.
