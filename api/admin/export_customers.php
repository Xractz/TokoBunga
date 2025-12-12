<?php
require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";
require_once __DIR__ . "/../../config/dompdf.php";

use Dompdf\Dompdf;
use Dompdf\Options;

global $conn;

$sql = "SELECT * FROM users WHERE role = 'customer' ORDER BY name ASC";
$result = mysqli_query($conn, $sql);

$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $uid = $row['id'];
    $sqlCount = "SELECT COUNT(*) as cnt FROM orders WHERE user_id = $uid";
    $resCount = mysqli_query($conn, $sqlCount);
    $cnt = mysqli_fetch_assoc($resCount);
    $row['total_transactions'] = $cnt['cnt'] ?? 0;
    
    $customers[] = $row;
}

$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Pelanggan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Data Pelanggan</h2>
    <p>Tanggal Cetak: ' . date("d-m-Y H:i") . '</p>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>No. HP</th>
                <th class="text-center">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>';

if (empty($customers)) {
    $html .= '<tr><td colspan="5" class="text-center">Tidak ada data pelanggan.</td></tr>';
} else {
    $no = 1;
    foreach ($customers as $c) {
        $html .= '<tr>
            <td class="text-center">' . $no++ . '</td>
            <td>' . htmlspecialchars($c['name']) . '</td>
            <td>' . htmlspecialchars($c['email']) . '</td>
            <td>' . htmlspecialchars($c['phone'] ?? '-') . '</td>
            <td class="text-center">' . $c['total_transactions'] . '</td>
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
$dompdf->stream("Laporan_Pelanggan_" . date("Ymd") . ".pdf", ["Attachment" => false]);
