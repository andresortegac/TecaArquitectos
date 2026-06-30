document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const sidebar = document.getElementById("main-sidebar");
  const menuButton = document.querySelector(".mobile-menu-toggle");
  const overlay = document.querySelector(".sidebar-overlay");

  // Menu lateral movil: abre/cierra con transicion y bloquea toques del fondo.
  const closeSidebar = () => {
    body.classList.remove("sidebar-open");
    menuButton?.setAttribute("aria-expanded", "false");
  };

  const openSidebar = () => {
    body.classList.add("sidebar-open");
    menuButton?.setAttribute("aria-expanded", "true");
  };

  menuButton?.addEventListener("click", () => {
    body.classList.contains("sidebar-open") ? closeSidebar() : openSidebar();
  });

  overlay?.addEventListener("click", closeSidebar);

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeSidebar();
  });

  sidebar?.querySelectorAll("a[href]:not([href='javascript:void(0)'])").forEach((link) => {
    link.addEventListener("click", () => {
      if (window.matchMedia("(max-width: 767px)").matches) closeSidebar();
    });
  });

  window.addEventListener("resize", () => {
    if (window.matchMedia("(min-width: 768px)").matches) closeSidebar();
  });
});
