<?php
require_once __DIR__ . '/../api/middleware/is_login.php';
require_once __DIR__ . '/../api/helpers/flash.php';
require_once '../config/auth.php';

// Get user data from session
$userId = getUserId();
$userName = $_SESSION['name'] ?? 'User';
$userEmail = $_SESSION['email'] ?? '';
$userPhone = $_SESSION['phone'] ?? '';
$userRole = $_SESSION['role'] ?? 'customer';

// Get flash messages
$error = flash('error');
$success = flash('success');
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Profil – Bloomify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display+SC:wght@400;700&family=Playfair+Display:wght@400;600;700&family=Playfair:wght@400;500&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap Icons (kalau dipakai) -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <!-- CSS utama kamu -->
    <link rel="stylesheet" href="assets/css/style.css" />
  </head>
  <body>
    <!-- NAVBAR -->
    <header class="header">
      <div class="container">
        <nav class="navbar">
          <div class="logo">
            <i class="bi bi-flower1"></i>
            <h1>Bloomify</h1>
          </div>

          <ul class="menu">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="katalog.php">Katalog Bunga</a></li>
            <li><a href="tentang.php">Tentang Kami</a></li>
            <?php if (isAdmin()) echo '<a href="admin/index.php">Admin Panel</a>'; ?>
          </ul>

          <div class="auth-buttons">
            <button class="icon-btn">
              <i class="bi bi-person-circle"></i>
            </button>
          </div>
        </nav>
      </div>
    </header>

    <!-- PROFILE PAGE -->
    <main class="container">
      <section class="profile-page">
        <div class="profile-layout">
          <!-- SIDEBAR -->
          <aside class="profile-sidebar">
            <div class="profile-sidebar-card">
              <div class="profile-avatar-wrapper">
                <div class="profile-avatar">
                  <img src="assets/images/profiles/default.png" alt="Foto profil" />
                </div>
                <p class="profile-name"><?php echo htmlspecialchars($userName); ?></p>
                <p class="profile-role"><?php echo ucfirst(htmlspecialchars($userRole)); ?></p>
              </div>

              <nav class="profile-menu">
                <a href="#" class="profile-menu-item active" data-tab="profile">
                  <i class="bi bi-person-fill"></i>
                  <span>Informasi Pribadi</span>
                </a>

                <a href="#" class="profile-menu-item" data-tab="password">
                  <i class="bi bi-key-fill"></i>
                  <span>Ganti Password</span>
                </a>

                <a href="/api/auth/logout.php" class="profile-menu-item">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Keluar</span>
                </a>
              </nav>
            </div>
          </aside>

          <!-- MAIN CONTENT -->
          <section class="profile-main-card">
            <div class="profile-main-header">
              <h2>Informasi Pribadi</h2>
              <p>
                Perbarui data akunmu untuk pengalaman belanja yang lebih nyaman.
              </p>
            </div>


            <!-- TAB: PROFILE -->
            <div id="tab-profile" class="profile-tab-content">
                <form action="#" method="post" class="profile-form">
                <input type="hidden" id="userId" value="<?php echo $userId; ?>" />

                <div class="profile-form-grid">
                    <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-icon">
                        <i class="bi bi-person-vcard"></i>
                        </span>
                        <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Nama Lengkap"
                        />
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <span class="input-icon">
                        <i class="bi bi-person"></i>
                        </span>
                        <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Username"
                        />
                    </div>
                    </div>

                    <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <span class="input-icon">
                        <i class="bi bi-envelope"></i>
                        </span>
                        <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Email"
                        />
                    </div>
                    </div>

                    <div class="form-group profile-form-full">
                    <label for="phone">Nomor Telepon</label>
                    <div class="input-group">
                        <span class="input-icon">
                        <i class="bi bi-telephone"></i>
                        </span>
                        <input
                        type="text"
                        id="phone"
                        name="phone"
                        placeholder="08xx-xxxx-xxxx"
                        />
                    </div>
                    </div>

                    <div class="form-group profile-form-full">
                    <label for="address">Alamat Lengkap</label>
                    <textarea
                        id="address"
                        name="address"
                        class="profile-textarea"
                        placeholder="Alamat lengkap..."
                    ></textarea>
                    </div>

                    <div class="form-group profile-form-full">
                    <label for="photo">Foto Profil</label>
                    <div class="profile-photo-row">
                        <div class="profile-photo-preview">
                        <img src="assets/images/profiles/default.png" alt="Foto profil" />
                        </div>

                        <div class="profile-upload-info">
                        <input
                            type="file"
                            id="photo"
                            name="profile_photo" 
                            accept="image/*"
                        />
                        </div>
                    </div>
                    </div>
                </div>

                <!-- tombol bawah -->
                <div class="profile-actions">
                    <button type="button" class="btn-profile-ghost" onclick="window.location.href='index.php'">
                    Batalkan Perubahan
                    </button>
                    <button type="submit" class="btn-profile-save">
                    Simpan Perubahan
                    </button>
                </div>
                </form>
            </div>

            <!-- TAB: PASSWORD -->
            <div id="tab-password" class="profile-tab-content" style="display: none;">
                <form action="#" method="post" class="password-form">
                    <div class="profile-form-grid">
                        <div class="form-group profile-form-full">
                            <label for="old_password">Password Lama</label>
                            <div class="input-group">
                                <span class="input-icon"><i class="bi bi-lock"></i></span>
                                <input type="password" id="old_password" name="old_password" placeholder="Masukkan password lama" required />
                            </div>
                        </div>

                        <div class="form-group profile-form-full">
                            <label for="new_password">Password Baru</label>
                            <div class="input-group">
                                <span class="input-icon"><i class="bi bi-key"></i></span>
                                <input type="password" id="new_password" name="new_password" placeholder="Minimal 6 karakter" required />
                            </div>
                        </div>

                        <div class="form-group profile-form-full">
                            <label for="confirm_password">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-icon"><i class="bi bi-check-circle"></i></span>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru" required />
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-actions">
                        <button type="submit" class="btn-profile-save">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
          </section>
        </div>
      </section>
    </main>
    <!-- FOOTER -->
    <footer class="footer">
      <div class="container">
        <div class="footer-top">
          <div class="footer-grid">
            <!-- Brand + text + sosmed -->
            <div class="footer-brand">
              <div class="footer-logo">
                <span class="footer-logo-icon">
                  <i class="bi bi-flower1"></i>
                </span>
                <span class="footer-logo-text">Bloomify</span>
              </div>
              <p>
                Bringing beauty and joy through exquisite floral arrangements,
                crafted with love and delivered with care.
              </p>

              <div class="footer-social">
                <a href="#" aria-label="Instagram">
                  <i class="bi bi-instagram"></i>
                </a>
                <a href="#" aria-label="Pinterest">
                  <i class="bi bi-pinterest"></i>
                </a>
                <a href="#" aria-label="Facebook">
                  <i class="bi bi-facebook"></i>
                </a>
              </div>
            </div>

            <!-- Shop -->
            <div class="footer-column">
              <h4>Shop</h4>
              <ul>
                <li><a href="#">All Flowers</a></li>
                <li><a href="#">Bouquets</a></li>
                <li><a href="#">Arrangements</a></li>
                <li><a href="#">Occasions</a></li>
                <li><a href="#">Gift Sets</a></li>
              </ul>
            </div>

            <!-- Customer Care -->
            <div class="footer-column">
              <h4>Customer Care</h4>
              <ul>
                <li><a href="#">My Account</a></li>
                <li><a href="#">Track Order</a></li>
                <li><a href="#">Delivery Info</a></li>
                <li><a href="#">Care Guide</a></li>
                <li><a href="#">Contact Us</a></li>
              </ul>
            </div>

            <!-- Stay Connected -->
            <div class="footer-column footer-newsletter">
              <h4>Stay Connected</h4>
              <p>
                Subscribe to get special offers, free giveaways, and floral
                inspiration.
              </p>
            </div>
          </div>

          <hr class="footer-divider" />

          <div class="footer-bottom">
            <p>© 2025 Bloomify. All rights reserved.</p>
            <div class="footer-bottom-links">
              <a href="#">Privacy Policy</a>
              <a href="#">Terms of Service</a>
              <a href="#">Returns</a>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- Custom JS -->
    <script src="assets/js/profile.js"></script>
  </body>
</html>