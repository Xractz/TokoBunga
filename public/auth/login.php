<?php
require_once __DIR__ . '/../../api/middleware/is_guest.php';
require_once __DIR__ . '/../../api/helpers/flash.php';
$error = flash('error');
$success = flash('success');
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../assets/images/favicon.png" type="image/png">
  <title>Login - Bloomify</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Playfair:wght@300;400;500&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

  <!-- Main CSS -->
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body class="auth-body">
  <main class="auth-page">
    <!-- CARD LOGIN -->
    <div class="auth-card">
      <!-- logo / icon -->
      <div class="auth-logo">
        <span class="auth-logo-circle">
          <i class="bi bi-flower3"></i>
        </span>
      </div>

      <!-- title & subtitle -->
      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-subtitle">Sign in to your Bloomify account</p>

      <!-- form login -->
      <form class="auth-form" id="loginForm">
        <?php if ($error): ?>
          <div class="alert-message alert-error">
            <i class="bi bi-exclamation-circle"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()">
              <i class="bi bi-x"></i>
            </button>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="alert-message alert-success">
            <i class="bi bi-check-circle"></i>
            <span><?php echo htmlspecialchars($success); ?></span>
          </div>
        <?php endif; ?>
        
        <!-- Email -->
        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-group">
            <span class="input-icon">
              <i class="bi bi-envelope"></i>
            </span>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Enter your email"
              required />
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-group password-wrapper">
            <span class="input-icon"><i class="bi bi-lock"></i></span>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Enter your password"
              required />
            <span
              class="toggle-password"
              onclick="togglePassword('password', this)">
              <i class="bi bi-eye-slash"></i>
            </span>
          </div>
        </div>

        <!-- Remember + Forgot -->
        <div class="auth-row">
          <a href="./forgot-password.html" class="forgot-link">Forgot Password?</a>
        </div>

        <!-- Tombol Sign In -->
        <button type="submit" class="btn-auth-primary">Sign In</button>

        <!-- Link ke register -->
        <p class="auth-footer-text">
          Don't have an account?
          <a href="./register.php">Create Account</a>
        </p>
      </form>
    </div>
  </main>

  <script src="../assets/js/script.js"></script>
  <script src="../assets/js/auth/login.js"></script>
</body>

</html>