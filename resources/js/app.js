import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import permissions from './plugins/permissions';
import roleDirective from '@/directives/role';
import permissionDirective from '@/directives/permission';

// Configuración PrimeVue
import 'primeicons/primeicons.css';
import PrimeVue from 'primevue/config';
import { primeVueConfig, primeVueComponents } from '@/config/primevue.config';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(permissions)
            .use(PrimeVue, primeVueConfig)
            .directive('role', roleDirective)
            .directive('permission', permissionDirective);

        // Registrar componentes de PrimeVue
        Object.entries(primeVueComponents).forEach(([name, component]) => {
            app.component(name, component);
        });

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
