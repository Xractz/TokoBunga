// Hamburger menu toggle untuk tentang.html
const hamburgerBtn = document.getElementById("hamburgerBtn");
const mobileMenu = document.getElementById("mobileMenu");

if (hamburgerBtn && mobileMenu) {
  hamburgerBtn.addEventListener("click", () => {
    mobileMenu.classList.toggle("open");
  });
}

// Tooltip hover effect untuk team cards
document.addEventListener("DOMContentLoaded", () => {
  const teamCardWrappers = document.querySelectorAll(".team-card-wrapper");

  teamCardWrappers.forEach((wrapper) => {
    const tooltip = wrapper.querySelector(".team-tooltip");

    if (tooltip) {
      wrapper.addEventListener("mouseenter", () => {
        tooltip.style.opacity = "1";
        tooltip.style.transform = "translate(-50%, -100%)";
      });

      wrapper.addEventListener("mouseleave", () => {
        tooltip.style.opacity = "0";
        tooltip.style.transform = "translate(-50%, -110%) translateY(10px)";
      });
    }
  });
});
