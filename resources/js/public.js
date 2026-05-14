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
        knob.classList.toggle('translate-x-11', isDark);
        knob.classList.toggle('translate-x-0', !isDark);
    });

    document.querySelectorAll('.js-theme-toggle').forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
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
    const panel = menu.querySelector('[data-mobile-menu-panel]');

    const setOpen = (open) => {
        menu.classList.toggle('hidden', !open);
        menu.classList.toggle('flex', open);
        document.documentElement.classList.toggle('overflow-hidden', open);
        document.body.classList.toggle('overflow-hidden', open);
        toggle.setAttribute('aria-expanded', String(open));

        if (open) {
            window.setTimeout(() => {
                panel?.querySelector('a, button')?.focus?.();
            }, 0);
        }
    };

    toggle.addEventListener('click', () => {
        setOpen(menu.classList.contains('hidden'));
    });

    document.querySelectorAll('[data-menu-close]').forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });

    menu.addEventListener('click', (event) => {
        if (event.target === menu) {
            setOpen(false);
        }
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

const parseJson = (value, fallback = []) => {
    if (!value) {
        return fallback;
    }

    try {
        const parsed = JSON.parse(value);
        return Array.isArray(parsed) ? parsed : fallback;
    } catch {
        return fallback;
    }
};

const normalizePreviewItems = (items) => items
    .map((item, index) => ({
        src: typeof item?.src === 'string' ? item.src : '',
        title: typeof item?.title === 'string' && item.title.trim() !== '' ? item.title : `Preview ${index + 1}`,
    }))
    .filter((item) => item.src !== '');

const initArmadaGallery = () => {
    const root = document.querySelector('[data-armada-gallery]');
    if (!root) {
        return;
    }

    const mainTrigger = root.querySelector('[data-preview-open]');
    const mainImage = root.querySelector('[data-gallery-main-image]');
    const thumbs = Array.from(root.querySelectorAll('[data-gallery-thumb]'));
    const items = normalizePreviewItems(parseJson(root.dataset.armadaImages));

    if (!mainTrigger || !mainImage || !thumbs.length || !items.length) {
        return;
    }

    let currentIndex = 0;

    const syncThumbState = (activeIndex) => {
        thumbs.forEach((thumb, index) => {
            const isActive = index === activeIndex;
            thumb.classList.toggle('border-primary', isActive);
            thumb.classList.toggle('ring-2', isActive);
            thumb.classList.toggle('ring-primary/25', isActive);
            thumb.classList.toggle('shadow-[0_0_0_1px_rgba(225,106,55,0.08)]', isActive);
            thumb.classList.toggle('border-slate-200', !isActive);
            thumb.classList.toggle('dark:border-slate-700', !isActive);
            thumb.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    };

    const setActiveImage = (index) => {
        currentIndex = (index + items.length) % items.length;
        const item = items[currentIndex];

        mainImage.src = item.src;
        mainImage.alt = `${item.title} - gambar ${currentIndex + 1}`;
        mainTrigger.dataset.previewIndex = String(currentIndex);
        mainTrigger.dataset.previewSrc = item.src;
        syncThumbState(currentIndex);
    };

    thumbs.forEach((thumb, index) => {
        const activateThumb = () => setActiveImage(index);

        thumb.addEventListener('mouseenter', activateThumb);
        thumb.addEventListener('focusin', activateThumb);
        thumb.addEventListener('click', activateThumb);
    });

    setActiveImage(0);
};

const initPreviewModal = () => {
    const modal = document.querySelector('[data-preview-modal]');
    const image = document.querySelector('[data-preview-image]');
    const title = document.querySelector('[data-preview-caption]');
    const count = document.querySelector('[data-preview-count]');
    const prev = document.querySelector('[data-preview-prev]');
    const next = document.querySelector('[data-preview-next]');
    const triggers = Array.from(document.querySelectorAll('[data-preview-open], [data-preview-src]'));
    if (!modal || !image) {
        return;
    }

    let items = [];
    let currentIndex = 0;
    let touchStartX = 0;

    const buildItems = (trigger) => {
        if (trigger.dataset.previewItems) {
            return normalizePreviewItems(parseJson(trigger.dataset.previewItems));
        }

        const galleryRoot = trigger.closest('[data-armada-gallery]');
        const galleryItems = normalizePreviewItems(parseJson(galleryRoot?.dataset.armadaImages));
        if (galleryItems.length) {
            return galleryItems;
        }

        return normalizePreviewItems(triggers.reduce((collection, button) => {
            if (!collection.some((item) => item.src === button.dataset.previewSrc)) {
                collection.push({
                    src: button.dataset.previewSrc,
                    title: button.dataset.previewTitle || image.alt || 'Preview Armada',
                });
            }

            return collection;
        }, []));
    };

    const showImage = (index) => {
        if (!items.length) {
            return;
        }

        currentIndex = (index + items.length) % items.length;
        const item = items[currentIndex];
        image.src = item.src;
        image.alt = item.title;

        if (title) {
            title.textContent = item.title;
        }

        if (count) {
            count.textContent = `${currentIndex + 1} / ${items.length}`;
        }

        const hasMultipleImages = items.length > 1;
        prev?.classList.toggle('hidden', !hasMultipleImages);
        next?.classList.toggle('hidden', !hasMultipleImages);
    };

    const setOpen = (open, index = 0) => {
        if (open) {
            showImage(index);
        } else {
            image.removeAttribute('src');
        }

        modal.classList.toggle('hidden', !open);
        modal.classList.toggle('flex', open);
        document.body.classList.toggle('overflow-hidden', open);
    };

    triggers.forEach((button) => {
        button.addEventListener('click', () => {
            items = buildItems(button);
            if (!items.length) {
                return;
            }

            const fallbackIndex = button.dataset.previewIndex ? Number(button.dataset.previewIndex) : 0;
            const matchedIndex = button.dataset.previewSrc
                ? items.findIndex((item) => item.src === button.dataset.previewSrc)
                : fallbackIndex;
            const index = Number.isFinite(matchedIndex) && matchedIndex >= 0 ? matchedIndex : fallbackIndex;

            setOpen(true, Math.max(0, index));
        });
    });

    document.querySelectorAll('[data-preview-close]').forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });

    prev?.addEventListener('click', () => showImage(currentIndex - 1));
    next?.addEventListener('click', () => showImage(currentIndex + 1));

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            setOpen(false);
        }
    });

    modal.addEventListener('touchstart', (event) => {
        touchStartX = event.changedTouches[0]?.clientX || 0;
    }, { passive: true });

    modal.addEventListener('touchend', (event) => {
        const touchEndX = event.changedTouches[0]?.clientX || 0;
        const distance = touchEndX - touchStartX;

        if (Math.abs(distance) < 48 || items.length < 2) {
            return;
        }

        showImage(distance > 0 ? currentIndex - 1 : currentIndex + 1);
    }, { passive: true });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }

        if (!modal.classList.contains('hidden') && event.key === 'ArrowLeft') {
            showImage(currentIndex - 1);
        }

        if (!modal.classList.contains('hidden') && event.key === 'ArrowRight') {
            showImage(currentIndex + 1);
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
initArmadaGallery();
initPreviewModal();

if (import.meta.env.PROD) {
    attachFirstInteraction();
    runWhenIdle(bootAnalytics);
    runWhenIdle(prefetchPopularPages);
}
