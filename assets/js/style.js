/* ================================
   TOGGLE PASSWORD (SHOW / HIDE)
================================ */
function togglePassword(inputId, el) {
  const input = document.getElementById(inputId);
  const icon = el.querySelector("i");

  if (!input || !icon) return;

  const isHidden = input.type === "password";

  input.type = isHidden ? "text" : "password";

  // Ganti icon
  icon.classList.toggle("bi-eye", isHidden);
  icon.classList.toggle("bi-eye-slash", !isHidden);
}

/* ================================
   HAMBURGER MENU DROPDOWN
================================ */
document.addEventListener("DOMContentLoaded", function () {
  const hamburger = document.getElementById("hamburgerBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  if (!hamburger || !mobileMenu) return;

  hamburger.addEventListener("click", function (e) {
    e.stopPropagation(); // biar klik di tombol nggak ikut dianggap klik "di luar"
    mobileMenu.classList.toggle("open");
    hamburger.classList.toggle("open");

    const icon = hamburger.querySelector("i");
    if (hamburger.classList.contains("open")) {
      icon.classList.remove("bi-list");
      icon.classList.add("bi-x");
    } else {
      icon.classList.remove("bi-x");
      icon.classList.add("bi-list");
    }
  });

  // Tutup kalau klik di luar dropdown
  document.addEventListener("click", function (e) {
    if (!mobileMenu.contains(e.target) && !hamburger.contains(e.target)) {
      mobileMenu.classList.remove("open");
      hamburger.classList.remove("open");

      const icon = hamburger.querySelector("i");
      if (icon) {
        icon.classList.remove("bi-x");
        icon.classList.add("bi-list");
      }
    }
  });
});
