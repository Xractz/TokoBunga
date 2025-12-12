<?php
require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../../config/db.php";
requireCustomer();

require_once __DIR__ . "/../../config/dompdf.php";

use Dompdf\Dompdf;
use Dompdf\Options;
global $conn;
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'customer';
$isAdmin = ($role === 'admin');

$order_code = $_GET['order_code'] ?? '';

if (!$order_code) {
    die("Order code missing.");
}

$sql = "SELECT * FROM orders WHERE order_code = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $order_code);
mysqli_stmt_execute($stmt);
$order = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

if (!$order) {
    die("Order not found.");
}

if (!$isAdmin && $order['user_id'] != $user_id) {
    http_response_code(403);
    die("Access denied. You do not own this invoice.");
}
$sqlItems = "SELECT oi.*, p.name as product_name FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?";
$stmtI = mysqli_prepare($conn, $sqlItems);
mysqli_stmt_bind_param($stmtI, "i", $order['id']);
mysqli_stmt_execute($stmtI);
$resI = mysqli_stmt_get_result($stmtI);
$items = [];
while ($row = $resI->fetch_assoc()) {
    $items[] = $row;
}
mysqli_stmt_close($stmtI);

$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #d63384; }
        .meta { margin-bottom: 20px; }
        .meta table { width: 100%; border: none; }
        .meta td { padding: 4px; }
        .table-items { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table-items th, .table-items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table-items th { background-color: #f8f8f8; }
        .totals { margin-top: 20px; text-align: right; }
        .totals table { margin-left: auto; width: 40%; }
        .totals td { padding: 5px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bloomify</h1>
        <p>Order Invoice</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="150" style="font-weight: bold;">Order Code:</td>
                <td>' . htmlspecialchars($order['order_code']) . '</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Date:</td>
                <td>' . date("d M Y H:i", strtotime($order['created_at'])) . '</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Customer:</td>
                <td>' . htmlspecialchars($order['recipient_name']) . '</td>
            </tr>
             <tr>
                <td style="font-weight: bold;">Address:</td>
                <td>' . htmlspecialchars($order['shipping_address']) . '</td>
            </tr>
        </table>
    </div>

    <table class="table-items">
        <thead>
            <tr>
                <th>Product</th>
                <th width="80">Price</th>
                <th width="50">Qty</th>
                <th width="100">Subtotal</th>
            </tr>
        </thead>
        <tbody>';

foreach ($items as $item) {
    $html .= '<tr>
        <td>' . htmlspecialchars($item['product_name']) . '</td>
        <td>Rp ' . number_format($item['unit_price'], 0, ',', '.') . '</td>
        <td>' . $item['quantity'] . '</td>
        <td>Rp ' . number_format($item['subtotal'], 0, ',', '.') . '</td>
    </tr>';
}

$html .= '</tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td align="right">Rp ' . number_format($order['subtotal'], 0, ',', '.') . '</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td align="right">Rp ' . number_format($order['shipping_cost'], 0, ',', '.') . '</td>
            </tr>
            <tr>
                <td style="font-weight: bold; font-size: 16px;">Total</td>
                <td align="right" style="font-weight: bold; font-size: 16px;">Rp ' . number_format($order['grand_total'], 0, ',', '.') . '</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for shopping with Bloomify!</p>
    </div>
</body>
</html>';

$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

$dompdf->render();
$dompdf->stream("Invoice-" . $order['order_code'] . ".pdf", ["Attachment" => false]);
