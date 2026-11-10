// Runs immediately after the rail, before the main content is painted.
(() => {
    const rail = document.querySelector('.shell > .rail');
    if (!rail) return;

    const key = `progress-navigation:v2:${rail.dataset.navigationKey}`;
    const desktop = window.matchMedia('(min-width:761px)');
    const menu = rail.querySelector('#menu');
    if (!menu) return;
    let saved;

    // Navigation still works when browser storage is unavailable.
    try {
        saved = JSON.parse(sessionStorage.getItem(key));
    } catch {}

    menu.open = desktop.matches || saved?.menuOpen === true;
    if (desktop.matches) {
        if (Number.isFinite(saved?.top)) {
            menu.scrollTop = saved.top;
        } else {
            const current = menu.querySelector('[aria-current="page"]');
            if (current) {
                const item = current.getBoundingClientRect();
                const viewport = menu.getBoundingClientRect();
                if (item.bottom > viewport.bottom || item.top < viewport.top) {
                    menu.scrollTop += item.top - viewport.top - viewport.height / 2;
                }
            }
        }
    }

    const save = () => {
        try {
            saved = {
                top: desktop.matches ? menu.scrollTop : (saved?.top ?? 0),
                menuOpen: desktop.matches ? (saved?.menuOpen ?? false) : menu.open,
            };
            sessionStorage.setItem(key, JSON.stringify(saved));
        } catch {}
    };

    menu.addEventListener('scroll', save, { passive: true });
    rail.addEventListener('click', save);
    menu?.addEventListener('toggle', save);
    window.addEventListener('pagehide', save);
    desktop.addEventListener('change', () => {
        menu.open = desktop.matches || saved?.menuOpen === true;
        if (desktop.matches) menu.scrollTop = saved?.top ?? 0;
    });
})();
