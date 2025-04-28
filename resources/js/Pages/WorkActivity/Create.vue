<script setup>
// >>>>>>>>> Importaciones
import { ref, computed, watch, defineProps } from 'vue';
import { Head, usePage, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatDistance, subDays } from "date-fns";

import {
    Avatar,
    FloatLabel,
    Chip,
    SelectButton,
    Tabs,
    TabList,
    Tab,
    TabPanels,
    TabPanel,
    ToggleButton,
    Textarea,
    DatePicker,
    InputNumber,
} from "primevue";

import { useAuthCache } from '@/composables/useAuthCache';

import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from "primevue/useconfirm";

import { FilterMatchMode } from '@primevue/core/api';

const props = defineProps({
    work_days: {
        type: Array,
        default: () => [],
    }
});

const work_days_ref = ref([...props.work_days]);
// Convertir a Date cuando sea necesario
const formattedDates = work_days_ref.value.map(dateStr => new Date(dateStr));
// Si necesitas ordenarlas (las Date son comparables)
const sortedDates = formattedDates.sort((a, b) => a - b);

const use_form_work_activity = useForm({
    title: ``,
    subtitle: ``,
    description: ``,
    start_time: new Date(),
    end_time: new Date(),
    duration_hours: 0,
    notes: ``,
    tags: [],
    status: 'pending',
    work_day: {
        date: new Date(),
        hourly_rate: 0.0,
        total_events: 0,
        total_hours: 0,
        amount: 0,
        billable: 0,
        status: 'pending',
        details: '',
        note: '',
        tags: [],
    },
    work_event: {
        editable: true,
        title: '',
        allDay: false,
        start: new Date(),
        end: new Date(),
        url: '',
        classnames: '',
        backgroundColor: '',
        borderColor: '',
        textColor: '',
        overlap: true,
        editable: true,
        startEditable: true,
        durationEditable: true,
        display: 'auto',
    }
});

// INICIO de lógica de programación
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
const toast = useToast();
const pastDate = ref(subDays(new Date(), 15));

// Inicialización segura de tags
const initialTags = () => {
    try {
        return Array.isArray(use_form_work_activity.tags)
            ? use_form_work_activity.tags
            : JSON.parse(use_form_work_activity.tags || "[]");
    } catch (error) {
        return [];
    }
};

const tags = ref(initialTags());
const newTag = ref("");

const work_activity_status = ref(use_form_work_activity.status);
const work_activity_options = ref([
    "pending",
    "approved",
    "rejected",
    "paid",
    "unpaid",
]);

const event_locked = ref(use_form_work_activity.work_event.editable);

const addTag = () => {
    if (newTag.value.trim() && !tags.value.includes(newTag.value.trim())) {
        tags.value.push(newTag.value.trim());
        newTag.value = "";
    }
};

const removeTag = (index) => {
    if (index >= 0 && index < tags.value.length) {
        tags.value.splice(index, 1);
        // Actualiza el formulario si es necesario
        use_form_work_activity.tags = [...tags.value];
    }
};

const getSeverity = (status) => {
    switch (status) {
        case "approved":
            return "success";
        case "rejected":
            return "danger";
        case "paid":
            return "success";
        case "unpaid":
            return "warning";
        default:
            return "info"; // pending
    }
};

const getColor = (status) => {
    switch (status) {
        case "approved":
            return "lime";
        case "rejected":
            return "red";
        case "paid":
            return "green";
        case "unpaid":
            return "lightgray";
        default:
            return ""; // pending
    }
};

const getIcon = (status) => {
    switch (status) {
        case "approved":
            return "pi pi-check";
        case "rejected":
            return "pi pi-times";
        case "paid":
            return "pi pi-dollar";
        case "unpaid":
            return "pi pi-exclamation-circle";
        default:
            return "pi pi-clock"; // pending
    }
};

// * Funciones HTTP's
const submitCreateWorkActivity = () => {
    use_form_work_activity
    .transform((data) => ({
        ...data,
        start_time: use_form_work_activity.start_time.toLocaleString(),
        end_time: use_form_work_activity.end_time.toLocaleString(),
    }))
    .post(route('work-activities.store'));
}

// * Computadas
const durationHours = computed(() => {
  if (!use_form_work_activity.start_time || !use_form_work_activity.end_time) {
    return 0;
  }

  const start = new Date(use_form_work_activity.start_time);
  const end = new Date(use_form_work_activity.end_time);

  if (end < start) {
    end.setDate(end.getDate() + 1);
  }

  const diffMs = end - start;
  const totalHours = diffMs / (1000 * 60 * 60);
  use_form_work_activity.duration_hours = totalHours;

  return totalHours; // Trunca decimales
});

// * Observadores
// Sincroniza tags con el formulario (si es necesario)
watch(
    tags,
    (newTags) => {
        // O JSON.stringify(newTags) si espera un string
        use_form_work_activity.tags = newTags;
    },
    { deep: true }
);

watch(
    event_locked,
    (newEventLocked) => {
        use_form_work_activity.work_event.editable = newEventLocked;
    },
    { deep: true }
);

watch(
    work_activity_status,
    (new_work_activity_status) => {
        const colorSelected = getColor(new_work_activity_status);
        use_form_work_activity.work_event.backgroundColor = colorSelected;
        use_form_work_activity.work_event.borderColor = colorSelected;
    },
    { deep: true }
);

// FIN de lógica de programación
// # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
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
                    <div class="flex justify-between items-center gap-4">
                        <Button label="Volver" severity="secondary" icon="pi pi-arrow-left" iconPos="left" @click.stop="
                            router.visit(route('work-activities.index'))
                            " />
                        <div class="text-center text-xl font-semibold">
                            Crear actividad
                        </div>
                    </div>

                    <Divider align="center" type="dotted">
                        <b>OR</b>
                    </Divider>

                    <Tabs value="0">
                        <TabList>
                            <Tab value="0" as="div" class="flex items-center gap-2">
                                <Avatar icon="pi pi-face-smile" shape="circle" />
                                <span class="font-bold whitespace-nowrap">Actividad</span>
                            </Tab>
                            <Tab value="1" as="div" class="flex items-center gap-2">
                                <Avatar icon="pi pi-briefcase" shape="circle" />
                                <span class="font-bold whitespace-nowrap">Día trabajado</span>
                            </Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel value="0">
                                <div class="flex flex-col gap-2 mt-4" style="min-height: 100%; max-width: 100%">
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <InputText :disabled="!event_locked" :invalid="use_form_work_activity
                                                    ?.errors?.title
                                                " v-model="use_form_work_activity.title
                                                    " size="small" id="title" aria-describedby="title-help" fluid />
                                            <label for="title">Titulo</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.title,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.title
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <InputText :disabled="!event_locked" :invalid="use_form_work_activity
                                                    ?.errors?.subtitle
                                                " v-model="use_form_work_activity.subtitle
                                                    " size="small" id="subtitle" aria-describedby="subtitle-help" fluid />
                                            <label for="subtitle">Subtitulo</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.subtitle,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.subtitle
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :disabled="!event_locked" :invalid="use_form_work_activity
                                                    ?.errors?.description
                                                " v-model="use_form_work_activity.description
                                                    " size="small" id="description" aria-describedby="description-help"
                                                fluid rows="5" cols="30" style="resize: none" />
                                            <label for="description">Descripción</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.description,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.description
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :disabled="!event_locked" :invalid="use_form_work_activity
                                                    ?.errors?.notes
                                                " v-model="use_form_work_activity.notes
                                                    " size="small" id="notes" aria-describedby="notes-help" fluid rows="5"
                                                cols="30" style="resize: none" />
                                            <label for="notes">Notas</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.notes,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.notes
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <InputText :disabled="!event_locked" :invalid="use_form_work_activity
                                                    ?.errors?.tags
                                                " v-model="newTag" @keydown.enter="addTag" size="small" id="tags"
                                                aria-describedby="tags-help" fluid />
                                            <label for="tags">Etiquetas (Enter para añadir
                                                más)</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.tags,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.tags
                                            }}</Message>
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            <Chip v-for="(tag, index) in tags" :key="`${tag}-${index}`" icon="pi pi-tag"
                                                :label="tag" :removable="event_locked" @remove="removeTag(index)" />
                                        </div>
                                    </div>
                                </div>
                            </TabPanel>
                            <TabPanel value="1">
                                <div class="flex flex-col gap-2 mt-4" style="min-height: 100%; max-width: 100%">
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <DatePicker  inputId="over_work_day_date"
                                                :min-date="pastDate"
                                                :max-date="use_form_work_activity.work_day.date"
                                                :disabled-dates="sortedDates"
                                                inline showWeek class="w-full sm:w-[30rem]"
                                                showIcon :default-value="new Date(
                                                    use_form_work_activity.work_day.date
                                                )
                                                    " iconDisplay="input" style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="over_work_day_date">Día trabajo</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.work_day?.date,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.work_day?.date
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <DatePicker :disabled="!event_locked" v-model="use_form_work_activity.start_time
                                                " inputId="over_work_activity_start_time" showIcon iconDisplay="input"
                                                timeOnly style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="over_work_activity_start_time">Hora inicial (formato 24 hrs)</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.start_time,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.start_time
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <DatePicker :disabled="!event_locked" v-model="use_form_work_activity.end_time
                                                " inputId="over_work_activity_end_time" showIcon iconDisplay="input"
                                                timeOnly style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="over_work_activity_end_time">Hora final (formato 24 hrs)</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.start_time,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.start_time
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <InputNumber :disabled="true" :invalid="false" v-model="durationHours
                                                " inputId="minmax-buttons" mode="decimal" showButtons :min="0"
                                                :max="100" fluid style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="minmax-buttons">Duración en horas</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.subtitle,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.subtitle
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :disabled="!event_locked" :invalid="use_form_work_activity
                                                    ?.errors?.work_day?.details
                                                " v-model="use_form_work_activity
                                                        .work_day.details
                                                    " size="small" id="work_day_details" aria-describedby="work_day_details-help"
                                                fluid rows="5" cols="30" style="resize: none" />
                                            <label for="work_day_details">Detalles</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.work_day?.details,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.work_day?.details
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :disabled="!event_locked" :invalid="use_form_work_activity
                                                    ?.errors?.work_day?.note
                                                " v-model="use_form_work_activity
                                                        .work_day.note
                                                    " size="small" id="work_day_note" aria-describedby="work_day_note-help"
                                                fluid rows="5" cols="30" style="resize: none" />
                                            <label for="work_day_note">Notas</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !use_form_work_activity
                                                ?.errors?.work_day?.note,
                                        }" severity="error" variant="simple" size="small">{{
                                                use_form_work_activity?.errors
                                                    ?.work_day?.note
                                            }}</Message>
                                    </div>
                                    <div v-if="false" class="flex flex-col gap-2 field m-4">
                                        <label for="over_work_day_billable">¿Es pagable el día?</label>
                                        <ToggleButton :disabled="!event_locked" v-model="use_form_work_activity.work_day
                                                .billable
                                            " onIcon="pi pi-check" offIcon="pi pi-times"
                                            inputId="over_work_day_billable" size="large" class="w-full sm:w-40"
                                            aria-label="Confirmation" />
                                    </div>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>

                    <div v-if="false" class="flex flex-col gap-2" style="min-height: 100%; max-width: 100%">
                        <div class="flex justify-between gap-2 field">
                            <SelectButton :disabled="!event_locked" v-model="work_activity_status"
                                :options="work_activity_options" size="large" />
                            <ToggleButton :disabled="!event_locked" v-model="event_locked" onLabel="Locked" offLabel="Unlocked"
                                onIcon="pi pi-lock-open" offIcon="pi pi-lock" class="w-36"
                                aria-label="Do you confirm" />
                        </div>
                    </div>

                    <div v-if="use_form_work_activity.work_event.editable" class="flex justify-start mt-4 gap-2">
                        <Button label="Crear" severity="success" icon="pi pi-save" iconPos="left"
                            @click="submitCreateWorkActivity()" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
