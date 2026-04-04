import './bootstrap';

import Alpine from 'alpinejs';
import { inject } from '@vercel/analytics';

window.Alpine = Alpine;

Alpine.start();

inject({
    mode: import.meta.env.PROD ? 'production' : 'development',
});
