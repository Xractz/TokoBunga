<?php

$envPath = __DIR__ . '/../.env';

if (!file_exists($envPath)) {
  die("Error: File konfigurasi .env tidak ditemukan di: $envPath");
}

$env = parse_ini_file($envPath);

if ($env === false || empty($env)) {
  die("Error: Gagal membaca file konfigurasi .env atau file kosong.");
}
