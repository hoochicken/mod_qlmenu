/**
 * mod_qlmenu
 *
 * @copyright  Copyright (C) 2026. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

if (!window.Joomla) {
  throw new Error('Joomla API was not properly initialised');
}
document.addEventListener('DOMContentLoaded', function () {
  const menu = document.getElementsByClassName('mod-qlmenu-ul');

  // Handle all toggle buttons (2nd level open/close)
  menu.querySelectorAll('.mod-qlmenu__toggle-sub').forEach(button => {
    button.addEventListener('click', function (e) {
      e.stopPropagation();

      const expanded = this.getAttribute('aria-expanded') === 'true';
      const submenuIds = this.getAttribute('aria-controls').split(' ');

      submenuIds.forEach(id => {
        const submenu = document.getElementById(id);
        if (!submenu) return;

        submenu.style.display = expanded ? 'none' : 'block';
        submenu.setAttribute('aria-hidden', expanded ? 'true' : 'false');

        if (!expanded) {
          positionSubmenu(submenu);
        }
      });

      this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
    });
  });

  // Close menu when clicking outside
  document.addEventListener('click', function () {
    menu.querySelectorAll('.mod-menu__sub').forEach(sub => {
      sub.style.display = 'none';
      sub.setAttribute('aria-hidden', 'true');
    });

    menu.querySelectorAll('.mod-qlmenu__toggle-sub').forEach(btn => {
      btn.setAttribute('aria-expanded', 'false');
    });
  });

  function positionSubmenu(submenu) {
    const parentLi = submenu.parentElement;
    const rect = submenu.getBoundingClientRect();

    // Reset first
    submenu.style.left = '';
    submenu.style.right = '';

    // Detect if it's 3rd level (nested inside another submenu)
    const isThirdLevel = parentLi.closest('.mod-menu__sub');

    if (isThirdLevel) {
      // Default: open LEFT
      submenu.style.left = 'auto';
      submenu.style.right = '100%';

      // Check if there's enough space on the left
      const leftSpace = rect.left;
      if (leftSpace < 200) {
        // Not enough space → open RIGHT instead
        submenu.style.right = 'auto';
        submenu.style.left = '100%';
      }
    } else {
      // 2nd level: always open downward
      submenu.style.left = '0';
      submenu.style.top = '100%';
    }
  }
});

