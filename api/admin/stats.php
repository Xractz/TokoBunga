<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/auth.php";
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../middleware/is_admin.php";

global $conn;

// Default periode perminggu
$period = $_GET['period'] ?? 'week';

$startDate = '';
$endDate = date('Y-m-d 23:59:59');

if ($period === 'week') {
    $startDate = date('Y-m-d 00:00:00', strtotime('monday this week'));
} elseif ($period === 'month') {
    $startDate = date('Y-m-01 00:00:00');
    $endDate = date('Y-m-t 23:59:59');
} elseif ($period === 'year') {
    $startDate = date('Y-01-01 00:00:00');
    $endDate = date('Y-12-31 23:59:59');
} else {
    $startDate = date('Y-m-d 00:00:00', strtotime('monday this week'));
}

$sql_transactions = "SELECT COUNT(*) as total_qty FROM orders 
                     WHERE created_at BETWEEN ? AND ? 
                     AND status != 'cancelled'";
$stmt_trans = mysqli_prepare($conn, $sql_transactions);
mysqli_stmt_bind_param($stmt_trans, "ss", $startDate, $endDate);
mysqli_stmt_execute($stmt_trans);
$res_trans = mysqli_stmt_get_result($stmt_trans);
$row_trans = mysqli_fetch_assoc($res_trans);
$totalTransactions = $row_trans['total_qty'] ?? 0;
mysqli_stmt_close($stmt_trans);

$sql_revenue = "SELECT SUM(grand_total) as total_rev FROM orders 
                WHERE created_at BETWEEN ? AND ? 
                AND payment_status = 'paid'
                AND status != 'cancelled'";
$stmt_rev = mysqli_prepare($conn, $sql_revenue);
mysqli_stmt_bind_param($stmt_rev, "ss", $startDate, $endDate);
mysqli_stmt_execute($stmt_rev);
$res_rev = mysqli_stmt_get_result($stmt_rev);
$row_rev = mysqli_fetch_assoc($res_rev);
$totalRevenue = $row_rev['total_rev'] ?? 0;
mysqli_stmt_close($stmt_rev);

respondJson(200, true, "Stats data retrieved", [
    'period' => $period,
    'start_date' => $startDate,
    'end_date' => $endDate,
    'total_transactions' => (int)$totalTransactions,
    'total_revenue' => (float)$totalRevenue
]);
