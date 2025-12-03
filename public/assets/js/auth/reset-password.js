document.addEventListener("DOMContentLoaded", function () {
  const resetForm = document.getElementById("resetPasswordForm");

  if (!resetForm) return;

  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get('token');
  
  const tokenRegex = /^[a-f0-9]{64}$/i;
  
  if (!token || !tokenRegex.test(token)) {
    window.location.href = "/";
    return;
  }

  document.getElementById('token').value = token;

  resetForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(resetForm);
    const newPassword = formData.get("newPassword");
    const confirmPassword = formData.get("confirmPassword");
    const submitBtn = resetForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;

    clearErrors();

    if (newPassword !== confirmPassword) {
      showError("Password dan konfirmasi password tidak cocok");
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Resetting...";

    try {
      const response = await fetch("/api/auth/update-password.php", {
        method: "POST",
        body: formData,
      });

      const contentType = response.headers.get("content-type");
      if (!contentType || !contentType.includes("application/json")) {
        throw new Error("Server returned invalid response");
      }

      const data = await response.json();

      if (response.ok) {
        showSuccess(data.message || "Password berhasil direset.");

        resetForm.reset();

        setTimeout(() => {
          window.location.href = "/public/auth/login.html";
        }, 3000);
      } else {
        let errorMessage = data.message || "Failed to reset password. Please try again.";

        if (data.redirect) {
          setTimeout(() => {
            window.location.href = "/";
          }, 1500);
        }

        switch (response.status) {
          case 400:
            errorMessage = data.message || "Semua field wajib diisi dengan benar";
            break;
          case 404:
            setTimeout(() => {
              window.location.href = "/";
            }, 2000);
            errorMessage = data.message || "Token reset tidak valid atau sudah kadaluarsa";
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
      console.error("Reset password error:", error);
      showError("Network error. Please check your connection and try again.");
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  });
});


