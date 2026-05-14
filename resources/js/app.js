import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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

const runWhenIdle = (callback) => {
    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(callback, { timeout: 2500 });
        return;
    }

    window.setTimeout(callback, 1500);
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
    const paths = ['/paket', '/katalog', '/paket-tour', '/rental-hiace', '/travel-pinrang'];

    paths.forEach((path) => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = path;
        document.head.appendChild(link);
    });
};

if (import.meta.env.PROD) {
    attachFirstInteraction();
    runWhenIdle(bootAnalytics);
    runWhenIdle(prefetchPopularPages);
}
