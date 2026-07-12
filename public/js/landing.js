document.addEventListener("DOMContentLoaded", () => {
  const navbar = document.getElementById("libraryNavbar");
  const links = document.querySelectorAll(".library-navbar .nav-link");
  const revealItems = document.querySelectorAll(".reveal");
  const menu = document.getElementById("mainMenu");

  const setNavbarState = () => {
    navbar?.classList.toggle("is-solid", window.scrollY > 24);
  };

  const setActiveLink = () => {
    const currentPosition = window.scrollY + 130;
    links.forEach((link) => {
      const section = document.querySelector(link.getAttribute("href"));
      if (!section) return;
      const active = section.offsetTop <= currentPosition && section.offsetTop + section.offsetHeight > currentPosition;
      link.classList.toggle("active", active);
    });
  };

  links.forEach((link) => {
    link.addEventListener("click", () => {
      const collapse = bootstrap.Collapse.getInstance(menu);
      if (collapse) collapse.hide();
    });
  });

  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    revealItems.forEach((item) => observer.observe(item));
  } else {
    revealItems.forEach((item) => item.classList.add("is-visible"));
  }

  window.addEventListener("scroll", () => {
    setNavbarState();
    setActiveLink();
  }, { passive: true });

  setNavbarState();
  setActiveLink();
});
