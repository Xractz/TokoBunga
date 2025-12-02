function togglePassword(inputId, el) {
  const input = document.getElementById(inputId);
  const icon = el.querySelector("i");

  if (input.type === "password") {
    // password sedang tidak terlihat → BUAT terlihat
    input.type = "text";
    icon.classList.remove("bi-eye-slash");
    icon.classList.add("bi-eye");
  } else {
    // password sedang terlihat → BUAT tersembunyi
    input.type = "password";
    icon.classList.remove("bi-eye");
    icon.classList.add("bi-eye-slash");
  }
}
