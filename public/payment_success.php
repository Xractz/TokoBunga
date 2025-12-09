<?php
require_once __DIR__ . '/../config/auth.php';
requireCustomer();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - Bloomify</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .success-page {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            padding: 2rem;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .success-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #333;
        }
        .success-desc {
            color: #666;
            margin-bottom: 2rem;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header class="header">
      <div class="container">
        <nav class="navbar">
          <div class="logo">
            <i class="bi bi-flower1"></i>
            <h1>Bloomify</h1>
          </div>
          <ul class="menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="katalog.php">Katalog Bunga</a></li>
          </ul>
           <div class="auth-buttons">
            <button
              class="icon-btn"
              aria-label="Cart"
              onclick="window.location.href='cart.php'"
            >
              <i class="bi bi-bag"></i>
              <span id="cartCount" class="cart-badge hidden">0</span>
            </button>
          </div>
        </nav>
      </div>
    </header>

    <main class="container success-page">
    <?php
    require_once '../config/db.php';
    global $conn;

    $orderCode = $_GET['order_code'] ?? '';
    
    // Verify order exists and belongs to user
    $validOrder = false;
    if ($orderCode) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM orders WHERE order_code = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $orderCode, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $validOrder = true;
        }
        mysqli_stmt_close($stmt);
    }
    
    if ($validOrder):
    ?>
        <i class="bi bi-check-circle-fill success-icon"></i>
        <h1 class="success-title">Pembayaran Berhasil!</h1>
        <p class="success-desc">Terima kasih telah berbelanja di Bloomify. Pesanan <strong><?php echo htmlspecialchars($orderCode); ?></strong> sedang diproses.</p>
        
        <p id="clearingStatus" style="font-size: 0.9rem; color: #999;">Memproses data...</p>

        <a href="katalog.php" class="btn btn-primary" style="margin-top: 20px;">Belanja Lagi</a>

        <script src="assets/js/script.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", async function() {
                // Clear cart immediately
                try {
                    const response = await fetch('/api/cart/clear.php', { method: 'POST' });
                    const result = await response.json();
                    if(result.success) {
                        document.getElementById('clearingStatus').innerText = "Jangan sampai ketinggalan, pesanan Anda sedang diproses!";
                         updateCartBadge();
                    }
                } catch (e) {
                    console.error("Gagal clear cart", e);
                }
            });
        </script>
    <?php else: ?>
        <i class="bi bi-x-circle-fill" style="font-size: 5rem; color: #dc3545; margin-bottom: 1rem;"></i>
        <h1 class="success-title">Akses Ditolak</h1>
        <p class="success-desc">Data transaksi tidak ditemukan atau Anda tidak memiliki akses ke halaman ini.</p>
        <a href="index.php" class="btn btn-secondary" style="margin-top: 20px;">Kembali ke Home</a>
    <?php endif; ?>
    </main>

</body>
</html>
