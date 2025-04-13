// Configuración PrimeVue
import 'primeicons/primeicons.css';
import PrimeVue from 'primevue/config';
import { primeVueConfig, primeVueComponents } from '@/config/primevue.config';
import { defineAsyncComponent } from 'vue';
import ToastService from 'primevue/toastservice';

/*
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import Card from 'primevue/card';
import Button from 'primevue/button';
*/

export default {
    install(app) {
        // Registrar PrimeVue como plugin
        app.use(PrimeVue, primeVueConfig);

        // Registrar servicio Toast
        app.use(ToastService);

        /*
        app.component('DataTable', DataTable);
        app.component('Column', Column);
        app.component('ColumnGroup', ColumnGroup);
        app.component('Row', Row);
        app.component('Card', Card);
        app.component('Button', Button);
        */

        // Registrar componentes
        for (const [name, component] of Object.entries(primeVueComponents)) {
            app.component(name, component);
        }
    }
};
