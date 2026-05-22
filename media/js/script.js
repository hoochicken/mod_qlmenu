document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".mod-qlmenu-ul").forEach(menu => {
    const mobileButton = menu.previousElementSibling;

    if (
      mobileButton &&
      mobileButton.classList.contains("mod-qlmenu-mobile-toggle")
    ) {
      mobileButton.addEventListener("click", function () {
        const isOpen = menu.classList.toggle("is-open");
        mobileButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
      });
    }

    menu.querySelectorAll(".mod-menu__sub").forEach(submenu => {
      submenu.style.display = "none";
    });

    menu.querySelectorAll(".mod-qlmenu__toggle-sub").forEach(button => {
      button.addEventListener("click", function (event) {
        event.preventDefault();
        event.stopPropagation();

        const parentLi = button.closest("li");
        const submenu = parentLi.querySelector(":scope > .mod-menu__sub");
        const isOpen = button.getAttribute("aria-expanded") === "true";

        closeSiblingMenus(parentLi);

        if (submenu) {
          submenu.style.display = isOpen ? "none" : "block";
          button.setAttribute("aria-expanded", isOpen ? "false" : "true");

          if (!isOpen && window.innerWidth >= 768) {
            positionSubmenu(submenu);
          }
        }
      });
    });

    document.addEventListener("click", function () {
      if (window.innerWidth >= 768) {
        closeAllMenus(menu);
      }
    });

    menu.addEventListener("click", function (event) {
      event.stopPropagation();
    });
  });

  function closeSiblingMenus(currentLi) {
    const parentUl = currentLi.parentElement;

    parentUl.querySelectorAll(":scope > li").forEach(li => {
      if (li === currentLi) return;

      li.querySelectorAll(".mod-menu__sub").forEach(submenu => {
        submenu.style.display = "none";
      });

      li.querySelectorAll(".mod-qlmenu__toggle-sub").forEach(button => {
        button.setAttribute("aria-expanded", "false");
      });
    });
  }

  function closeAllMenus(menu) {
    menu.querySelectorAll(".mod-menu__sub").forEach(submenu => {
      submenu.style.display = "none";
    });

    menu.querySelectorAll(".mod-qlmenu__toggle-sub").forEach(button => {
      button.setAttribute("aria-expanded", "false");
    });
  }

  function positionSubmenu(submenu) {
    const parentLi = submenu.parentElement;
    const isNested = parentLi.closest(".mod-menu__sub");

    submenu.style.left = "";
    submenu.style.right = "";
    submenu.style.top = "";

    if (isNested) {
      // submenu.style.top = "0";
      // submenu.style.right = "100%";
      submenu.style.left = "auto";

      const rect = submenu.getBoundingClientRect();

      if (rect.left < 0) {
        submenu.style.right = "auto";
        submenu.style.left = "100%";
      }
    } else {
      submenu.style.top = "100%";
      submenu.style.left = "0";
    }
  }
});
