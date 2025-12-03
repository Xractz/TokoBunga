<?php
require 'config.php';

global $conn;

$username = $env['DB_USER'];
$password = $env['DB_PASSWORD'];
$hostname = $env['DB_HOST'];
$database = $env['DB_NAME'];

$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
  die("Error: Gagal terhubung ke database. " . mysqli_connect_error());
}