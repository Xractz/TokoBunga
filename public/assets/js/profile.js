document.addEventListener("DOMContentLoaded", function () {
  // --- TAB SWITCHING LOGIC ---
  const menuItems = document.querySelectorAll(".profile-menu-item[data-tab]");
  const tabContents = document.querySelectorAll(".profile-tab-content");
  const pageTitle = document.getElementById("page-title");
  const pageDesc = document.getElementById("page-desc");

  menuItems.forEach((item) => {
    item.addEventListener("click", function (e) {
      e.preventDefault();
      const target = this.getAttribute("data-tab");

      // Update Menu Active State
      menuItems.forEach((i) => i.classList.remove("active"));
      this.classList.add("active");

      // Update Tab Visibility
      tabContents.forEach((content) => {
        content.style.display =
          content.id === `tab-${target}` ? "block" : "none";
      });

      // Update Header Text (Optional)
      if (pageTitle && pageDesc) {
        if (target === "password") {
          pageTitle.textContent = "Ganti Password";
          pageDesc.textContent =
            "Jaga keamanan akunmu dengan password yang kuat.";
        } else {
          pageTitle.textContent = "Informasi Pribadi";
          pageDesc.textContent =
            "Perbarui data akunmu untuk pengalaman belanja yang lebih nyaman.";
        }
      }
    });
  });

  const profileForm = document.querySelector(".profile-form");
  const userIdInput = document.getElementById("userId");
  const userId = userIdInput ? userIdInput.value : null;

  if (!userId) {
    console.error("User ID not found, data fetching might fail.");
  }

  // Load Profile Data
  async function loadProfile() {
    if (!userId) return; // Guard here instead
    try {
      const response = await fetch(`/api/users/list.php?id=${userId}`);
      const result = await response.json();

      if (result.success && result.data && result.data.length > 0) {
        const user = result.data[0];

        // Populate Form
        document.getElementById("username").value = user.username || "";
        document.getElementById("email").value = user.email || "";
        document.getElementById("phone").value = user.phone || "";
        document.getElementById("address").value = user.address || "";

        // Update sidebar info if elements exist
        const sideName = document.querySelector(".profile-name");
        const sideRole = document.querySelector(".profile-role");
        if (sideName) sideName.textContent = user.name || user.username;
        if (sideRole)
          sideRole.textContent =
            user.role.charAt(0).toUpperCase() + user.role.slice(1);

        const photoUrl = user.profile_photo
          ? `assets/images/profiles/${user.profile_photo}`
          : `assets/images/profiles/default.png`;

        const preview = document.querySelector(".profile-photo-preview img");
        if (preview) preview.src = photoUrl;

        const sidebarAvatar = document.querySelector(".profile-avatar img");
        if (sidebarAvatar) sidebarAvatar.src = photoUrl;
      } else {
        showAlert("error", result.message || "Gagal memuat data profil.");
      }
    } catch (error) {
      console.error("Error loading profile:", error);
      showAlert("error", "Terjadi kesalahan saat memuat data.");
    }
  }

  // Handle Form Submit
  if (profileForm) {
    profileForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const submitBtn = profileForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.textContent;
      submitBtn.textContent = "Menyimpan...";
      submitBtn.disabled = true;

      try {
        const formData = new FormData(profileForm);
        // API expects POST
        const response = await fetch("/api/users/update.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          showAlert("success", result.message || "Profil berhasil diperbarui.");
          // Reload to refresh potential photo changes or sidebar updates
          loadProfile();
        } else {
          showAlert("error", result.message || "Gagal memperbarui profil.");
        }
      } catch (error) {
        console.error("Error updating profile:", error);
        showAlert("error", "Terjadi kesalahan saat menyimpan perubahan.");
      } finally {
        submitBtn.textContent = originalBtnText;
        submitBtn.disabled = false;
      }
    });
  }

  // Image Preview Handler
  const photoInput = document.getElementById("photo");
  const photoPreview = document.querySelector(".profile-photo-preview img");

  if (photoInput && photoPreview) {
    photoInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          photoPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // --- PASSWORD FORM HANDLER ---
  const passwordForm = document.querySelector(".password-form");
  if (passwordForm) {
    passwordForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const submitBtn = passwordForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.textContent;
      submitBtn.textContent = "Memproses...";
      submitBtn.disabled = true;

      const oldPass = document.getElementById("old_password").value;
      const newPass = document.getElementById("new_password").value;
      const confirmPass = document.getElementById("confirm_password").value;

      if (newPass !== confirmPass) {
        showAlert("error", "Konfirmasi password tidak cocok.");
        submitBtn.textContent = originalBtnText;
        submitBtn.disabled = false;
        return;
      }

      try {
        const formData = new FormData(passwordForm);
        const response = await fetch("/api/users/change_password.php", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          showAlert("success", result.message || "Password berhasil diubah.");
          passwordForm.reset();
        } else {
          showAlert("error", result.message || "Gagal mengubah password.");
        }
      } catch (error) {
        console.error("Error changing password:", error);
        showAlert("error", "Terjadi kesalahan sistem.");
      } finally {
        submitBtn.textContent = originalBtnText;
        submitBtn.disabled = false;
      }
    });
  }

  // Alert Helper
  function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll(".alert-message");
    existingAlerts.forEach((el) => el.remove());

    const alertDiv = document.createElement("div");
    alertDiv.className = `alert-message alert-${type}`;

    const iconClass =
      type === "success" ? "bi-check-circle" : "bi-exclamation-circle";

    alertDiv.innerHTML = `
            <i class="bi ${iconClass}"></i>
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        `;

    const container = document.querySelector(".profile-main-header");
    if (container) {
      container.after(alertDiv);
    }

    // Auto hide
    setTimeout(() => {
      if (alertDiv.parentElement) {
        alertDiv.style.opacity = "0";
        setTimeout(() => alertDiv.remove(), 300);
      }
    }, 5000);
  }

  // Initial Load
  loadProfile();
});
