import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('[data-mobile-nav]').forEach((nav) => {
    const button = nav.querySelector('[data-mobile-nav-toggle]');
    const menu = nav.querySelector('[data-mobile-nav-menu]');

    if (!button || !menu) {
        return;
    }

    const closeMenu = () => {
        button.setAttribute('aria-expanded', 'false');
        menu.classList.add('hidden');
    };

    const openMenu = () => {
        button.setAttribute('aria-expanded', 'true');
        menu.classList.remove('hidden');
    };

    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';

        if (expanded) {
            closeMenu();
            return;
        }

        openMenu();
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('click', (event) => {
        if (!nav.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            closeMenu();
        }
    });
});
