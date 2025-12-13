<?php
require_once "../config/db.php";
global $conn;

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$error = '';
$success = '';
$showForm = false;

if ($token === '') {
    $error = 'Token tidak valid.';
} else {
    // 1. Verify Token on Load (and before Post)
    $stmt = mysqli_prepare($conn, "SELECT id, reset_token_expires FROM users WHERE reset_token = ?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        $error = 'Link reset password tidak valid atau sudah digunakan.';
    } elseif (strtotime($user['reset_token_expires']) <= time()) {
        $error = 'Link reset password sudah kadaluarsa.';
    } else {
        // Token valid
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass = $_POST['newPassword'] ?? '';
            $conf = $_POST['confirmPassword'] ?? '';

            if (strlen($pass) < 6) {
                $error = 'Password minimal 6 karakter.';
                $showForm = true;
            } elseif ($pass !== $conf) {
                $error = 'Konfirmasi password tidak cocok.';
                $showForm = true;
            } else {
                // Update Password
                $hashed = password_hash($pass, PASSWORD_DEFAULT);
                $stmt2 = mysqli_prepare($conn, "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
                mysqli_stmt_bind_param($stmt2, "si", $hashed, $user['id']);
                if (mysqli_stmt_execute($stmt2)) {
                    $success = 'Password berhasil direset. Silakan login menggunakan password baru.';
                } else {
                    $error = 'Gagal mengupdate password. Silakan coba lagi.';
                    $showForm = true;
                }
            }
        } else {
            $showForm = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - Bloomify</title>
  <link rel="icon" href="assets/images/favicon.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:ital,wght@0,400;0,700;1,400&family=Playfair:ital,wght@0,500;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
  <main class="auth-page">
    <section class="auth-card">
      <div class="auth-logo">
        <div class="auth-logo-circle" style="color: var(--accent-dark);">
          <i class="bi bi-key"></i>
        </div>
      </div>

      <h1 class="auth-title">Reset Password</h1>

      <?php if ($success): ?>
        <div class="alert-message alert-success" style="display:flex; justify-content:center; margin-bottom:1rem;">
            <i class="bi bi-check-circle"></i> <span><?= htmlspecialchars($success) ?></span>
        </div>
        <button class="btn-auth-primary" onclick="window.location.href='auth/login.php'">Masuk ke Akun</button>
      
      <?php elseif ($error && !$showForm): ?>
         <div class="alert-message alert-error" style="display:flex; justify-content:center; margin-bottom:1rem;">
            <i class="bi bi-exclamation-circle"></i> <span><?= htmlspecialchars($error) ?></span>
        </div>
        <div class="auth-footer-text">
            <a href="index.php">Kembali ke Beranda</a>
        </div>

      <?php elseif ($showForm): ?>
        <p class="auth-subtitle">Masukkan password baru untuk akun Anda.</p>
        
        <?php if ($error): ?>
            <div class="alert-message alert-error" style="margin-bottom:1rem;">
                <i class="bi bi-exclamation-circle"></i> <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="form-group">
                <label>Password Baru</label>
                <div class="input-group">
                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                    <input type="password" name="newPassword" required minlength="6" placeholder="Minimal 6 karakter">
                </div>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirmPassword" required placeholder="Ulangi password baru">
                </div>
            </div>

            <button type="submit" class="btn-auth-primary">Ubah Password</button>
        </form>
      <?php endif; ?>

    </section>
  </main>
</body>
</html>
