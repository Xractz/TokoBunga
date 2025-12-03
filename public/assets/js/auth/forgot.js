document.addEventListener("DOMContentLoaded", function () {
  const forgotForm = document.getElementById("forgotPasswordForm");

  if (!forgotForm) return;

  forgotForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(forgotForm);
    const submitBtn = forgotForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.textContent = "Sending...";

    clearErrors();

    try {
      const response = await fetch("/api/auth/reset-password.php", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (response.ok) {
        showSuccess(data.message || "Link reset berhasil dikirim!");

        forgotForm.reset();

        setTimeout(() => {
          window.location.href = "/public/auth/login.html";
        }, 3000);
      } else {
        let errorMessage = data.message || "Gagal mengirim link reset. Silakan coba lagi.";

        switch (response.status) {
          case 400:
            errorMessage = data.message || "Email wajib diisi dengan benar";
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
      showError("Terjadi kesalahan jaringan. Silakan periksa koneksi Anda dan coba lagi.");
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  });
});


