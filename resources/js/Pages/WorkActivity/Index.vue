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

const calendarRef = ref(null);
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
    dateClick: function(info) {
        alert('Clicked on: ' + info.dateStr);
        alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
        alert('Current view: ' + info.view.type);
        // change the day's background color just for fun
        info.dayEl.style.backgroundColor = 'red';
    },
    navLinkDayClick(date, jsEvent) {
        // 1. Ejecuta tu lógica personalizada
        console.log('day', date.toISOString());
        console.log('coords', jsEvent.pageX, jsEvent.pageY);

        // 2. Navega manualmente a la vista de día
        // ✅ Accede a la API a través de la referencia
        if (calendarRef.value) {
            calendarRef.value.getApi().changeView('timeGridDay', date);
        }
    },
    eventDrop(info) {
        console.log(info);
        // Obtener las fechas original y nueva
        const originalStart = info.oldEvent.start;
        const newStart = info.event.start;
        const originalEnd = info.oldEvent.end;
        const newEnd = info.event.end;

        // Comprobar si el día ha cambiado
        if (
            originalStart.getDate() !== newStart.getDate() ||
            originalStart.getMonth() !== newStart.getMonth() ||
            originalStart.getFullYear() !== newStart.getFullYear() ||
            (originalEnd && newEnd && (
                originalEnd.getDate() !== newEnd.getDate() ||
                originalEnd.getMonth() !== newEnd.getMonth() ||
                originalEnd.getFullYear() !== newEnd.getFullYear()
            ))
        ) {
            // Revertir el cambio si el día es diferente
            info.revert();
            // Opcional: mostrar un mensaje al usuario
            alert('Solo se puede cambiar la hora, no el día del evento.');
        } else {
            // Aquí puedes manejar el cambio de hora si lo necesitas
            console.log('Hora cambiada:', {
                oldStart: originalStart,
                newStart: newStart,
                oldEnd: originalEnd,
                newEnd: newEnd
            });
        }
    },
    eventResize(info) {
        const originalStart = info.oldEvent.start;
        const newStart = info.event.start; // No cambia en resize
        const originalEnd = info.oldEvent.end;
        const newEnd = info.event.end;

        // Comprobar si el día del final ha cambiado
        if (originalEnd && newEnd && (
            originalEnd.getDate() !== newEnd.getDate() ||
            originalEnd.getMonth() !== newEnd.getMonth() ||
            originalEnd.getFullYear() !== newEnd.getFullYear()
        )) {
            info.revert();
            alert('Solo se puede cambiar la hora, no el día del evento.');
        } else {
            console.log('Duración cambiada:', {
                start: newStart,
                oldEnd: originalEnd,
                newEnd: newEnd
            });
        }
    },
    eventChange(info){
        console.log('eventChange.info => ', info);
        // Obtener el evento actualizado
        const event = info.event;
        // Obtener el evento actual
        const oldEvent = info.oldEvent;

        // Datos básicos del evento
        const eventData = {
            id: oldEvent.id,  // ID del evento
            title: oldEvent.title,  // Título del evento
            start: oldEvent.start,  // Fecha/hora de inicio (objeto Date)
            end: oldEvent.end,  // Fecha/hora de fin (objeto Date, puede ser null)
            allDay: oldEvent.allDay,  // Si es un evento de todo el día (booleano)

            // Propiedades extendidas (custom data que hayas añadido al evento)
            extendedProps: oldEvent.extendedProps,

            // Otros datos útiles
            backgroundColor: oldEvent.backgroundColor,
            borderColor: oldEvent.borderColor,
            textColor: oldEvent.textColor,
        };

        console.log('Todos los datos del evento:', eventData);
    },
    eventClick (info) {
        info.jsEvent.preventDefault(); // don't let the browser navigate
        const workActivityEditable = info.event.durationEditable;
        const workActivityId = info.event.extendedProps.work_activity_id;

        if (!workActivityId) {
            confirm("No se encontró work_activity_id en el evento");
            return;
        }else
        if(!workActivityEditable) {
            confirm("El work_activity_id en el evento está bloqueado");
            return;
        }

        // Redirige a la ruta de edición usando Inertia.js
        router.get(route('work-activities.edit', workActivityId));
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
                    <div class="py-4">
                        <Button security=""
                        @click.stop="router.visit(route('work-activities.create'))">
                            Crear actividad
                        </Button>
                    </div>
                    <FullCalendar ref="calendarRef" :options='calendarOptions'></FullCalendar>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
