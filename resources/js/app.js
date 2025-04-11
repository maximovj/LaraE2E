import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import permissions from './plugins/permissions';
import roleDirective from '@/directives/role';
import permissionDirective from '@/directives/permission';
import PrimeVuePlugin from './plugins/primevue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(permissions)
            .use(PrimeVuePlugin)
            .directive('role', roleDirective)
            .directive('permission', permissionDirective)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
