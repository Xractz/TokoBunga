<?php
require_once "../config/db.php";
global $conn;

$token = $_GET['token'] ?? '';
$status = 'invalid';

if ($token !== '') {
  $stmt = mysqli_prepare(
    $conn,
    "SELECT id, is_active FROM users WHERE activation_token = ?"
  );
  mysqli_stmt_bind_param($stmt, "s", $token);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_assoc($result);

  if ($user) {
    if ($user['is_active'] == 1) {
      $status = 'already_active';
    } else {
      $stmt = mysqli_prepare(
        $conn,
        "UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?"
      );
      mysqli_stmt_bind_param($stmt, "i", $user['id']);
      mysqli_stmt_execute($stmt);

      $status = 'success';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Playfair:ital,opsz,wght@0,5..1200,300..900;1,5..1200,300..900&display=swap"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="icon" href="assets/images/favicon.png" type="image/png">

  <title>Aktivasi Akun - Bloomify</title>
</head>

<body class="auth-body">
  <main class="auth-page">
    <section class="auth-card">

      <div class="auth-logo">
        <div class="auth-logo-circle" id="statusIcon">
          <i class="bi"></i>
        </div>
      </div>

      <h1 class="auth-title" id="statusTitle"></h1>
      <p class="auth-subtitle" id="statusMessage"></p>

      <button class="btn-auth-primary" id="actionButton"></button>

      <div class="auth-footer-text">
        atau <a href="index.php">kembali ke beranda</a>
      </div>
    </section>

    <p class="auth-terms-note" id="footerNote"></p>
  </main>

  <script>
    const status = "<?= $status ?>";

    const config = {
      success: {
        icon: "bi-check2-circle",
        color: "#22c55e",
        title: "Akun Berhasil Diaktivasi",
        message: "Akun kamu sekarang sudah aktif!",
        button: "Masuk ke Akun",
        action: "auth/login.php",
        footer: "Jika kamu merasa tidak membuat akun, abaikan email aktivasi ini."
      },
      already_active: {
        icon: "bi-info-circle",
        color: "#3b82f6",
        title: "Akun Sudah Aktif",
        message: "Akun kamu sudah pernah diaktifkan sebelumnya.",
        button: "Masuk ke Akun",
        action: "auth/login.php",
        footer: "Link aktivasi hanya bisa dipakai satu kali."
      },
      invalid: {
        icon: "bi-x-circle",
        color: "#ef4444",
        title: "Link Tidak Valid",
        message: "Link aktivasi tidak valid atau sudah kadaluarsa.",
        button: "Daftar Ulang",
        action: "auth/register.php",
        footer: "Silakan daftar ulang atau hubungi admin."
      }
    };

    const st = config[status] ?? config.invalid;

    document.getElementById("statusIcon").innerHTML =
      `<i class="bi ${st.icon}"></i>`;
    document.getElementById("statusIcon").style.color = st.color;
    document.getElementById("statusTitle").textContent = st.title;
    document.getElementById("statusMessage").textContent = st.message;

    const btn = document.getElementById("actionButton");
    btn.textContent = st.button;
    btn.onclick = () => window.location.href = st.action;

    document.getElementById("footerNote").textContent = st.footer;
  </script>
</body>

</html>