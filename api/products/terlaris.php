<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';

global $conn;

// ambil data dari table order_items join products (BEST SELLER)
$sqlBestSeller = "
    SELECT p.*, SUM(oi.quantity) as total_sold 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE p.is_active = 1
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 8
";

$stmt = mysqli_prepare($conn, $sqlBestSeller);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
$excludedIds = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
    $excludedIds[] = $row['id'];
}
mysqli_stmt_close($stmt);

// cek apakah jumlah produk sudah 8? Jika belum, isi sisanya dengan random produk
$needed = 8 - count($products);

if ($needed > 0) {
    $sqlRandom = "SELECT * FROM products WHERE is_active = 1";
    $params = [];
    $types = "";

    // exclude produk yang sudah diambil sebagai best seller
    if (!empty($excludedIds)) {
        $placeholders = implode(',', array_fill(0, count($excludedIds), '?'));
        $sqlRandom .= " AND id NOT IN ($placeholders)";
        $types .= str_repeat('i', count($excludedIds));
        $params = array_merge($params, $excludedIds);
    }
    
    $sqlRandom .= " ORDER BY RAND() LIMIT ?";
    $types .= "i";
    $params[] = $needed;
    
    $stmtRandom = mysqli_prepare($conn, $sqlRandom);
    if ($stmtRandom) {
        if (!empty($params)) {
             mysqli_stmt_bind_param($stmtRandom, $types, ...$params);
        }
        mysqli_stmt_execute($stmtRandom);
        $resRandom = mysqli_stmt_get_result($stmtRandom);
        
        while ($row = mysqli_fetch_assoc($resRandom)) {
            $row['total_sold'] = 0;
            $products[] = $row;
        }
        mysqli_stmt_close($stmtRandom);
    }
}

respondJson(200, true, 'Data best seller berhasil diambil.', $products);