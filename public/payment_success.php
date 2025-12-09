<?php
require_once __DIR__ . '/../config/auth.php';
requireCustomer();
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - Bloomify</title>
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
        .status-icon {
            font-size: 5rem;
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
        
        .btn {
            display: inline-block;
            font-family: "Playfair Display", serif;
            font-size: 1rem;
            padding: 0.75rem 2.5rem;
            border-radius: 15px;
            text-decoration: none;
            transition: 0.25s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
        }
        
        .btn-primary {
            background-color: #748b75;
            color: #ffffff;
        }
        
        .btn-primary:hover {
            background-color: #6a7f6c;
        }
        
        .btn-secondary {
            background-color: #f7efe4;
            color: #5b4736;
        }
        
        .btn-secondary:hover {
            background-color: #e3d5c5;
        }
    </style>
</head>
<body>
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
    global $conn;

    $orderCode = $_GET['order_code'] ?? '';
    
    $validOrder = false;
    $paymentStatus = '';
    
    if ($orderCode) {
        $stmt = mysqli_prepare($conn, "SELECT id, payment_status FROM orders WHERE order_code = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $orderCode, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $orderRow = mysqli_fetch_assoc($result);
        if ($orderRow) {
            $validOrder = true;
            $paymentStatus = $orderRow['payment_status'];
        }
        mysqli_stmt_close($stmt);
    }
    
    if ($validOrder):
        $isPaid = ($paymentStatus === 'paid');
        $iconClass = $isPaid ? 'bi-check-circle-fill' : 'bi-hourglass-split';
        $colorStyle = $isPaid ? 'color: #28a745;' : 'color: #ffc107;';
        
        $title = $isPaid ? 'Pembayaran Berhasil!' : 'Menunggu Pembayaran';
        
        if ($isPaid) {
            $desc = "Terima kasih! Pesanan <strong>" . htmlspecialchars($orderCode) . "</strong> telah lunas. Kami akan segera memproses pengiriman bunga Anda.";
        } else {
            $desc = "Pesanan <strong>" . htmlspecialchars($orderCode) . "</strong> telah dibuat. Silakan selesaikan pembayaran agar pesanan dapat diproses.";
        }
    ?>
        <i class="bi <?php echo $iconClass; ?> status-icon" style="<?php echo $colorStyle; ?>"></i>
        <h1 class="success-title"><?php echo $title; ?></h1>
        <p class="success-desc"><?php echo $desc; ?></p>
        
        <p id="clearingStatus" style="font-size: 0.9rem; color: #999;">
            <?php echo $isPaid ? "Pesanan Anda sedang diproses!" : "Menunggu konfirmasi sistem..."; ?>
        </p>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="katalog.php" class="btn btn-primary">Belanja Lagi</a>
            <?php if (!$isPaid): ?>
                <button onclick="window.location.reload()" class="btn btn-secondary">Cek Status</button>
            <?php endif; ?>
        </div>

        <script src="assets/js/script.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", async function() {
                try {
                    const response = await fetch('/api/cart/clear.php', { method: 'POST' });
                    const result = await response.json();
                    if(result.success) {
                      updateCartBadge();
                    }
                } catch (e) {
                    console.error("Gagal clear cart", e);
                }

                // Polling for Payment Status
                const orderCode = "<?php echo $orderCode; ?>";
                const isPaidInitial = <?php echo $isPaid ? 'true' : 'false'; ?>;
                
                if (!isPaidInitial && orderCode) {
                    let textDots = 0;
                    const statusTextEl = document.getElementById('clearingStatus');
                    
                    const intervalId = setInterval(async () => {
                        // Animasi titik-titik
                        textDots = (textDots + 1) % 4;
                        if(statusTextEl) statusTextEl.innerText = "Menunggu konfirmasi sistem" + ".".repeat(textDots);

                        try {
                            const res = await fetch(`/api/orders/status.php?order_code=${orderCode}`);
                            const data = await res.json();
                            
                            if (data.success && data.data.payment_status === 'paid') {
                                clearInterval(intervalId);
                                
                                const iconEl = document.querySelector('.status-icon');
                                const titleEl = document.querySelector('.success-title');
                                const descEl = document.querySelector('.success-desc');
                                const checkBtn = document.querySelector('.btn-secondary');
                                
                                if(iconEl) {
                                  iconEl.className = 'bi bi-check-circle-fill status-icon';
                                  iconEl.style.color = '#28a745';
                                }
                                
                                if(titleEl) titleEl.innerText = 'Pembayaran Berhasil!';
                                if(descEl) descEl.innerHTML = `Terima kasih! Pesanan <strong>${orderCode}</strong> telah lunas. Kami akan segera memproses pengiriman bunga Anda.`;
                                if(statusTextEl) statusTextEl.innerText = "Pesanan Anda sedang diproses!";
                                
                                if(checkBtn) checkBtn.remove();
                            }
                        } catch (err) {
                            console.error("Polling error", err);
                        }
                    }, 2000);
                }
            });
        </script>
    <?php else: ?>
        <i class="bi bi-x-circle-fill status-icon" style="color: #dc3545;"></i>
        <h1 class="success-title">Akses Ditolak</h1>
        <p class="success-desc">Data transaksi tidak ditemukan atau Anda tidak memiliki akses ke halaman ini.</p>
        <a href="index.php" class="btn btn-secondary" style="margin-top: 20px;">Kembali ke Home</a>
    <?php endif; ?>
    </main>

</body>
</html>
