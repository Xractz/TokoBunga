<?php
require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";
require_once __DIR__ . "/../../config/dompdf.php";

use Dompdf\Dompdf;
use Dompdf\Options;

global $conn;

// Filter by Current Month
$sqlStats = "SELECT COUNT(*) as total_trx, SUM(grand_total) as total_revenue 
             FROM orders 
             WHERE status != 'cancelled' 
             AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
             AND YEAR(created_at) = YEAR(CURRENT_DATE())";
$resStats = mysqli_query($conn, $sqlStats);
$stats = mysqli_fetch_assoc($resStats);

$sql = "SELECT * FROM orders 
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
        AND YEAR(created_at) = YEAR(CURRENT_DATE()) 
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

$monthName = date("F Y");

$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h2 { text-align: center; margin-bottom: 5px; }
        .summary-box { 
            border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; text-align: center; background: #f9f9f9;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Laporan Transaksi (' . $monthName . ')</h2>
    <p class="text-center">Tanggal Cetak: ' . date("d-m-Y H:i") . '</p>

    <div class="summary-box">
        <strong>Total Transaksi:</strong> ' . number_format($stats['total_trx']) . ' &nbsp; | &nbsp; 
        <strong>Total Pendapatan:</strong> Rp ' . number_format($stats['total_revenue'], 0, ',', '.') . '
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th>Kode Order</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th class="text-right">Total</th>
                <th>Status</th>
                <th>Pembayaran</th>
            </tr>
        </thead>
        <tbody>';

if (empty($orders)) {
    $html .= '<tr><td colspan="7" class="text-center">Tidak ada data transaksi.</td></tr>';
} else {
    $no = 1;
    foreach ($orders as $o) {
        $html .= '<tr>
            <td class="text-center">' . $no++ . '</td>
            <td>' . htmlspecialchars($o['order_code']) . '</td>
            <td>' . date("d M Y H:i", strtotime($o['created_at'])) . '</td>
            <td>' . htmlspecialchars($o['recipient_name']) . '</td>
            <td class="text-right">Rp ' . number_format($o['grand_total'], 0, ',', '.') . '</td>
            <td>' . ucfirst($o['status']) . '</td>
            <td>' . ucfirst($o['payment_status']) . '</td>
        </tr>';
    }
}

$html .= '</tbody>
    </table>
</body>
</html>';

$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("Laporan_Transaksi_" . date("Ymd") . ".pdf", ["Attachment" => false]);
