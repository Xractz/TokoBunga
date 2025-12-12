<?php
require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";
require_once __DIR__ . "/../../config/dompdf.php";

use Dompdf\Dompdf;
use Dompdf\Options;

global $conn;

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN product_categories c ON p.category_id = c.id 
        ORDER BY p.name ASC";
$result = mysqli_query($conn, $sql);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Generate HTML
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Daftar Produk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2>Laporan Daftar Produk</h2>
    <p>Tanggal Cetak: ' . date("d-m-Y H:i") . '</p>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-right">Harga</th>
                <th class="text-center">Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

if (empty($products)) {
    $html .= '<tr><td colspan="6" class="text-center">Tidak ada data produk.</td></tr>';
} else {
    $no = 1;
    foreach ($products as $p) {
        $status = ($p['stock'] > 0) ? 'Tersedia' : 'Habis';
        $html .= '<tr>
            <td class="text-center">' . $no++ . '</td>
            <td>' . htmlspecialchars($p['name']) . '</td>
            <td>' . htmlspecialchars($p['category_name'] ?? '-') . '</td>
            <td class="text-right">Rp ' . number_format($p['price'], 0, ',', '.') . '</td>
            <td class="text-center">' . $p['stock'] . '</td>
            <td>' . $status . '</td>
        </tr>';
    }
}

$html .= '</tbody>
    </table>
</body>
</html>';

// Render PDF
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Produk_" . date("Ymd") . ".pdf", ["Attachment" => false]);
