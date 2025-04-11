// Configuración PrimeVue
import 'primeicons/primeicons.css';
import PrimeVue from 'primevue/config';
import { primeVueConfig, primeVueComponents } from '@/config/primevue.config';
import { defineAsyncComponent } from 'vue';

export default {
    install(app) {
        // Registrar PrimeVue como plugin
        app.use(PrimeVue, primeVueConfig);

        // Registrar componentes
        for (const [name, component] of Object.entries(primeVueComponents)) {
            app.component(name, defineAsyncComponent(component));
        }
    }
};
