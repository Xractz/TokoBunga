document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");

  if (!registerForm) return;

  registerForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(registerForm);
    const password = formData.get("password");
    const confirmPassword = formData.get("confirmPassword");
    const submitBtn = registerForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;

    clearErrors();

    if (password !== confirmPassword) {
      showError("Password dan konfirmasi password tidak cocok");
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Creating Account...";

    try {
      const response = await fetch("/api/auth/register.php", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (response.ok) {
        showSuccess(data.message || "Registrasi berhasil!");

        registerForm.reset();

        setTimeout(() => {
          window.location.href = "/public/auth/login.html";
        }, 3000);
      } else {
        let errorMessage = data.message || "Registrasi gagal. Silakan coba lagi.";

        switch (response.status) {
          case 400:
            errorMessage = data.message || "Semua field wajib diisi dengan benar";
            break;
          case 409:
            errorMessage = data.message || "Email sudah terdaftar";
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
      console.error("Registration error:", error);
      showError("Terjadi kesalahan jaringan. Silakan periksa koneksi Anda dan coba lagi.");
      submitBtn.disabled = false;
      submitBtn.textContent = originalBtnText;
    }
  });
});


