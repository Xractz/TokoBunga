<?php
require_once __DIR__ . '/../../api/middleware/is_guest.php';
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register – Bloomora Floral</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Playfair:wght@300;400;500&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap Icons -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    />

    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>
  <body class="auth-body">
    <main class="auth-page">
      <div class="auth-card">
        <!-- logo / icon -->
        <div class="auth-logo">
          <span class="auth-logo-circle">
            <i class="bi bi-flower3"></i>
          </span>
        </div>

        <!-- title & subtitle -->
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-subtitle">
          Join Bloomify and enjoy an easier way to order your favorite bouquets.
        </p>

        <!-- form -->
        <form class="auth-form" id="registerForm">
          <!-- Full name -->
          <div class="form-group">
            <label for="fullName">Full Name</label>
            <div class="input-group">
              <span class="input-icon">
                <i class="bi bi-person"></i>
              </span>
              <input
                type="text"
                id="fullName"
                name="name"
                placeholder="Enter your full name"
                required
              />
            </div>
          </div>

          <!-- Username -->
          <div class="form-group">
            <label for="username">Username</label>
            <div class="input-group">
              <span class="input-icon">
                <i class="bi bi-at"></i>
              </span>
              <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                required
              />
            </div>
          </div>

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
                required
              />
            </div>
          </div>

          <!-- Phone -->
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <div class="input-group">
              <span class="input-icon">
                <i class="bi bi-telephone"></i>
              </span>
              <input
                type="tel"
                id="phone"
                name="phone"
                placeholder="Enter your phone number"
                required
              />
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
                required
              />
              <span
                class="toggle-password"
                onclick="togglePassword('password', this)"
              >
                <i class="bi bi-eye-slash"></i>
              </span>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <div class="input-group password-wrapper">
              <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
              <input
                type="password"
                id="confirmPassword"
                name="confirmPassword"
                placeholder="Re-enter your password"
                required
              />
              <span
                class="toggle-password"
                onclick="togglePassword('confirmPassword', this)"
              >
                <i class="bi bi-eye-slash"></i>
              </span>
            </div>
          </div>

          <!-- submit -->
          <button type="submit" class="btn-auth-primary">Create Account</button>

          <!-- footer link -->
          <p class="auth-footer-text">
            Already have an account?
            <a href="./login.php">Sign In</a>
          </p>
        </form>
      </div>
      <!-- small terms text -->
      <p class="auth-terms-note">
        By signing up, you agree to our
        <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
      </p>
    </main>

    <!-- JS (optional) -->
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/auth/register.js"></script>
  </body>
</html>
