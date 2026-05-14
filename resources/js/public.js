const runWhenIdle = (callback) => {
    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(callback, { timeout: 2500 });
        return;
    }

    window.setTimeout(callback, 1500);
};

const bootAnalytics = () => {
    if (window.__analyticsBooted) {
        return;
    }

    window.__analyticsBooted = true;
    import('@vercel/analytics')
        .then(({ inject }) => {
            inject({
                mode: import.meta.env.PROD ? 'production' : 'development',
            });
        })
        .catch(() => {
            window.__analyticsBooted = false;
        });
};

const applyTheme = (isDark) => {
    document.documentElement.classList.toggle('dark', isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');

    document.querySelectorAll('[data-logo-light][data-logo-dark]').forEach((logo) => {
        logo.src = isDark ? logo.dataset.logoDark : logo.dataset.logoLight;
    });

    document.querySelectorAll('[data-theme-knob]').forEach((knob) => {
        knob.classList.toggle('translate-x-8', isDark);
        knob.classList.toggle('translate-x-1', !isDark);
    });
};

const initTheme = () => {
    const saved = localStorage.getItem('theme');
    applyTheme(saved ? saved === 'dark' : false);

    document.querySelectorAll('.js-theme-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            applyTheme(!document.documentElement.classList.contains('dark'));
        });
    });
};

const initMobileMenu = () => {
    const menu = document.querySelector('[data-mobile-menu]');
    const toggle = document.querySelector('[data-menu-toggle]');
    if (!menu || !toggle) {
        return;
    }

    const setOpen = (open) => {
        menu.classList.toggle('hidden', !open);
        document.documentElement.classList.toggle('overflow-hidden', open);
        document.body.classList.toggle('overflow-hidden', open);
        toggle.setAttribute('aria-expanded', String(open));
    };

    toggle.addEventListener('click', () => {
        setOpen(menu.classList.contains('hidden'));
    });

    document.querySelectorAll('[data-menu-close]').forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
};

const initCatalogFilter = () => {
    const toggle = document.querySelector('[data-filter-toggle]');
    const panel = document.querySelector('[data-filter-panel]');
    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', () => {
        const open = panel.classList.toggle('hidden');
        toggle.setAttribute('aria-expanded', String(!open));
    });
};

const initPreviewModal = () => {
    const modal = document.querySelector('[data-preview-modal]');
    const image = document.querySelector('[data-preview-image]');
    if (!modal || !image) {
        return;
    }

    const setOpen = (open, src = '') => {
        image.src = src;
        modal.classList.toggle('hidden', !open);
        modal.classList.toggle('flex', open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    document.querySelectorAll('[data-preview-src]').forEach((button) => {
        button.addEventListener('click', () => setOpen(true, button.dataset.previewSrc));
    });

    document.querySelectorAll('[data-preview-close]').forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
};

const attachFirstInteraction = () => {
    const onceOptions = { once: true, passive: true };
    const trigger = () => runWhenIdle(bootAnalytics);

    window.addEventListener('pointerdown', trigger, onceOptions);
    window.addEventListener('keydown', trigger, onceOptions);
    window.addEventListener('touchstart', trigger, onceOptions);
    window.addEventListener('scroll', trigger, onceOptions);
};

const prefetchPopularPages = () => {
    ['/paket', '/katalog', '/paket-tour', '/rental-hiace', '/travel-pinrang'].forEach((path) => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = path;
        document.head.appendChild(link);
    });
};

initTheme();
initMobileMenu();
initCatalogFilter();
initPreviewModal();

if (import.meta.env.PROD) {
    attachFirstInteraction();
    runWhenIdle(bootAnalytics);
    runWhenIdle(prefetchPopularPages);
}
