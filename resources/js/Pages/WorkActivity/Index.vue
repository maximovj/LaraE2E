<script setup>
// Importaciones
import { ref, computed, watch, defineProps } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import multiMonthPlugin from '@fullcalendar/multimonth';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

import { useAuthCache } from '@/composables/useAuthCache';

import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from "primevue/useconfirm";

import { FilterMatchMode } from '@primevue/core/api';

// Lógica
const { getCurrentPermissions } = useAuthCache();

const props = defineProps({
    events: {
        type: Array,
        required: true,
        default: () => [] // Valor por defecto
    }
});

// Verificar permisos
const canCreate = computed(() => getCurrentPermissions().includes('employees.create'));
const canDelete = computed(() => getCurrentPermissions().includes('employees.delete'));
const canUpdate = computed(() => getCurrentPermissions().includes('employees.update'));
const canRead = computed(() => getCurrentPermissions().includes('employees.read'));

const calendarOptions = ref({
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
    locale: 'es',
    plugins: [
        multiMonthPlugin,
        timeGridPlugin,
        dayGridPlugin,
        listPlugin,
        interactionPlugin
    ],
    initialView: 'multiMonthYear',
    multiMonthMaxColumns: 1,
    editable: true,
    weekends: true,
    nowIndicator: true,
    navLinks: true,
    allDaySlot: true,
    headerToolbar: {
        start: 'title',
        center: '',
        right: 'multiMonthYear,timeGridWeek,timeGridOneDay,listWeek prev,next today'
    },
    buttonText: {
        today: 'Hoy',
        year: 'Anual',
        month: 'Mensual',
        week: 'Semanal',
        day: 'Diario',
        list: 'Lista',
    },
    views: {
        timeGridOneDay: {
            type: 'timeGrid',
            duration: { days: 1 }
        },
    },
    events: [
        ...props.events
    ],
    eventClick: function (info) {
        info.jsEvent.preventDefault(); // don't let the browser navigate

        /*
        //alert('Event: ' + info.event.title);
        //alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
        //alert('View: ' + info.view.type);

        console.log('info',info);
        console.log('info.el',info.el);
        console.log('info.event',info.event);
        console.log('info.jsEvent',info.jsEvent);
        console.log('info.view',info.view);

        // Acceder a work_activity_id
        const workActivityId = info.event.extendedProps.work_activity_id;
        console.log('Work Activity ID:', workActivityId);

        // También puedes acceder a otras propiedades estándar
        console.log('Event title:', info.event.title);
        console.log('Start date:', info.event.start);
        console.log('End date:', info.event.end);

        // change the border color just for fun
        info.el.style.borderColor = 'red';
        //info.el.fcSeg.eventRange.range.end = new Date('2025-04-22T14:30:00');
        //info.el.fcSeg.end = new Date('2025-04-22T14:30:00');
        */

        /*
        if (info.event.url) {
            window.open(info.event.url);
        }
        */

        const workActivityId = info.event.extendedProps.work_activity_id;
        if (workActivityId) {
            // Redirige a la ruta de edición usando Inertia.js
            router.get(route('work-activities.edit', workActivityId));
        } else {
            console.error("No se encontró work_activity_id en el evento");
        }
    }
});


</script>

<template>
    <Toast />
    <ConfirmPopup></ConfirmPopup>

    <Head title="Registro de actividades" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registro de actividades</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <FullCalendar :options='calendarOptions'></FullCalendar>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
