document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");

  if (!loginForm) return;

  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(loginForm);
    const submitBtn = loginForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.textContent = "Signing in...";

    clearErrors();

    try {
      const response = await fetch("/api/auth/login.php", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (response.ok) {
        showSuccess(data.message || "Login berhasil!");

        setTimeout(() => {
          if (data.role === "admin") {
            window.location.href = "/admin/dashboard.php";
          } else {
            window.location.href = "/";
          }
        }, 1000);
      } else {
        let errorMessage = data.message || "Login gagal. Silakan coba lagi.";
        
        switch (response.status) {
          case 401:
            errorMessage = data.message || "Password salah";
            break;
          case 403:
            errorMessage = data.message || "Akun belum diaktivasi";
            break;
          case 404:
            errorMessage = data.message || "Email tidak ditemukan";
            break;
          case 500:
            errorMessage = "Terjadi kesalahan pada server. Silakan coba lagi nanti.";
            break;
        }

        showError(errorMessage);
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
      }
    } catch (error) {
      console.error("Login error:", error);
      showError("Network error. Please check your connection and try again.");
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  });
});

function showError(message) {
  const existingAlert = document.querySelector(".alert-message");
  if (existingAlert) {
    existingAlert.remove();
  }

  const alert = document.createElement("div");
  alert.className = "alert-message alert-error";
  alert.innerHTML = `
    <i class="bi bi-exclamation-circle"></i>
    <span>${message}</span>
    <button class="alert-close" onclick="this.parentElement.remove()">
      <i class="bi bi-x"></i>
    </button>
  `;

  const form = document.getElementById("loginForm");
  if (form) {
    form.parentElement.insertBefore(alert, form);

    setTimeout(() => {
      if (alert.parentElement) {
        alert.remove();
      }
    }, 3000);
  }
}

function showSuccess(message) {
  const existingAlert = document.querySelector(".alert-message");
  if (existingAlert) {
    existingAlert.remove();
  }

  const alert = document.createElement("div");
  alert.className = "alert-message alert-success";
  alert.innerHTML = `
    <i class="bi bi-check-circle"></i>
    <span>${message}</span>
  `;

  const form = document.getElementById("loginForm");
  if (form) {
    form.parentElement.insertBefore(alert, form);
  }
}

function clearErrors() {
  const existingAlert = document.querySelector(".alert-message");
  if (existingAlert) {
    existingAlert.remove();
  }
}
