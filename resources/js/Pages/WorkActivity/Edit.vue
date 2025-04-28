<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { defineProps, ref, watch } from "vue";
import { parse } from 'date-fns';

import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from "primevue/useconfirm";

import { FilterMatchMode } from '@primevue/core/api';

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

const props = defineProps({
    work_activity: {
        type: Object,
        required: true,
    },
});

const user_form_work_activity = useForm({
    title: `${props.work_activity.title}`,
    subtitle: `${props.work_activity.subtitle}`,
    description: `${props.work_activity.description ?? ''}`,
    start_time: parse(props.work_activity.start_time, "HH:mm:ss", new Date(props.work_activity.work_event.start)),
    end_time: parse(props.work_activity.end_time, "HH:mm:ss", new Date(props.work_activity.work_event.end)),
    duration_hours: props.work_activity.duration_hours,
    notes: `${props.work_activity.notes}`,
    tags: props.work_activity.tags ?? [],
    status: props.work_activity.status,
    work_day: {
        date: new Date(props.work_activity.work_day.date),
        hourly_rate: props.work_activity.work_day.hourly_rate,
        total_events: props.work_activity.work_day.total_events,
        total_hours: props.work_activity.work_day.total_hours,
        amount: props.work_activity.work_day.amount,
        billable: props.work_activity.work_day.billable,
        status: props.work_activity.work_day.status,
        details: props.work_activity.work_day.details,
        note: props.work_activity.work_day.note,
        tags: props.work_activity.work_day.tags ?? [],
    },
    work_event: {
        editable: props.work_activity.work_event.editable,
        title: props.work_activity.work_event.title,
        allDay: props.work_activity.work_event.allDay,
        start: new Date(props.work_activity.work_event.start),
        end: new Date(props.work_activity.work_event.end),
        url: props.work_activity.work_event.url,
        classnames: props.work_activity.work_event.classnames,
        backgroundColor: props.work_activity.work_event.backgroundColor,
        borderColor: props.work_activity.work_event.borderColor,
        textColor: props.work_activity.work_event.textColor,
        overlap: props.work_activity.work_event.overlap,
        editable: props.work_activity.work_event.editable,
        startEditable: props.work_activity.work_event.startEditable,
        durationEditable: props.work_activity.work_event.durationEditable,
        display: props.work_activity.work_event.display,
    }
});

// Inicialización segura de tags
const initialTags = () => {
    try {
        return Array.isArray(props.work_activity.tags)
            ? props.work_activity.tags
            : JSON.parse(props.work_activity.tags || "[]");
    } catch (error) {
        return [];
    }
};

const tags = ref(initialTags());
const newTag = ref("");
const toast = useToast();
const confirm = useConfirm();

const work_activity_status = ref(props.work_activity.status);
const work_activity_options = ref([
    "pending",
    "approved",
    "rejected",
    "paid",
    "unpaid",
]);

const event_locked = ref(props.work_activity.work_event.editable);

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
        user_form_work_activity.tags = [...tags.value];
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
const confirmDelete = (event, user) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to proceed?',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Eliminar',
            severity: 'danger'
        },
        accept: () => {
            toast.add({
                severity: 'success',
                summary: 'Confirmado',
                detail: 'Acción aceptada correctamente', life: 3000
            });
            router.delete(route('work-activities.destroy', props.work_activity.id));
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Cancela', detail: 'Acción cancelada correctamente', life: 3000 });
        }
    });
}

const submitUpdateWorkActivity = () => {
    user_form_work_activity
    .transform((data) => ({
        ...data,
        start_time: user_form_work_activity.start_time.toLocaleString(),
        end_time: user_form_work_activity.end_time.toLocaleString(),
    }))
    .patch(route('work-activities.update', props.work_activity.id));
}

// * Observadores
// Sincroniza tags con el formulario (si es necesario)
watch(
    tags,
    (newTags) => {
        // O JSON.stringify(newTags) si espera un string
        user_form_work_activity.tags = newTags;
    },
    { deep: true }
);

watch(
    event_locked,
    (newEventLocked) => {
        user_form_work_activity.work_event.editable = newEventLocked;
    },
    { deep: true }
);

watch(
    work_activity_status,
    (new_work_activity_status) => {
        const colorSelected = getColor(new_work_activity_status);
        user_form_work_activity.work_event.backgroundColor = colorSelected;
        user_form_work_activity.work_event.borderColor = colorSelected;
    },
    { deep: true }
);
</script>

<template>
    <Toast />
    <ConfirmPopup></ConfirmPopup>

    <Head title="Modificar actividad" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modificar actividad
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center gap-4">
                        <Button label="Volver" severity="secondary" icon="pi pi-arrow-left" iconPos="left" @click.stop="
                            router.visit(route('work-activities.index'))
                            " />
                        <div class="text-center text-xl font-semibold">
                            Modificar actividad
                        </div>
                    </div>

                    <Divider align="center" type="dotted">
                        <b>OR</b>
                    </Divider>

                    <div class="flex flex-col gap-2" style="min-height: 100%; max-width: 100%">
                        <div class="flex flex-col gap-2 field">
                            <Tag :value="work_activity_status" :severity="getSeverity(work_activity_status)"
                                :icon="getIcon(work_activity_status)" style="height: 60px" />
                        </div>
                    </div>

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
                                            <InputText :disabled="!event_locked" :invalid="user_form_work_activity
                                                    .errors?.title
                                                " v-model="user_form_work_activity.title
                                                    " size="small" id="title" aria-describedby="title-help" fluid />
                                            <label for="title">Titulo</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.title,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.title
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <InputText :disabled="!event_locked" :invalid="user_form_work_activity
                                                    .errors?.subtitle
                                                " v-model="user_form_work_activity.subtitle
                                                    " size="small" id="subtitle" aria-describedby="subtitle-help" fluid />
                                            <label for="subtitle">Subtitulo</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.subtitle,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.subtitle
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :disabled="!event_locked" :invalid="user_form_work_activity
                                                    .errors?.description
                                                " v-model="user_form_work_activity.description
                                                    " size="small" id="description" aria-describedby="description-help"
                                                fluid rows="5" cols="30" style="resize: none" />
                                            <label for="description">Descripción</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.description,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.description
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :disabled="!event_locked" :invalid="user_form_work_activity
                                                    .errors?.notes
                                                " v-model="user_form_work_activity.notes
                                                    " size="small" id="notes" aria-describedby="notes-help" fluid rows="5"
                                                cols="30" style="resize: none" />
                                            <label for="notes">Notas</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.notes,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.notes
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <InputText :disabled="!event_locked" :invalid="user_form_work_activity
                                                    .errors?.tags
                                                " v-model="newTag" @keydown.enter="addTag" size="small" id="tags"
                                                aria-describedby="tags-help" fluid />
                                            <label for="tags">Etiquetas (Enter para añadir
                                                más)</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.tags,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
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
                                            <DatePicker :readonly="true" :disabled="true" inputId="over_work_day_date"
                                                showIcon :default-value="new Date(
                                                    user_form_work_activity.work_day.date
                                                )
                                                    " iconDisplay="input" style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="over_work_day_date">Día de trabajo</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.work_day?.date,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.work_day?.date
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <DatePicker :disabled="!event_locked" v-model="user_form_work_activity.start_time
                                                " inputId="over_work_activity_start_time" showIcon iconDisplay="input"
                                                timeOnly style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="over_work_activity_start_time">Hora inicial</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.start_time,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.start_time
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <DatePicker :disabled="!event_locked" v-model="user_form_work_activity.end_time
                                                " inputId="over_work_activity_end_time" showIcon iconDisplay="input"
                                                timeOnly style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="over_work_activity_end_time">Hora final</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.start_time,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.start_time
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <InputNumber :readonly="true" :disabled="true" v-model="user_form_work_activity.duration_hours
                                                " inputId="minmax-buttons" mode="decimal" showButtons :min="0"
                                                :max="100" fluid style="
                                                    min-width: 22rem;
                                                    max-width: 24px;
                                                " />
                                            <label for="minmax-buttons">Duración en horas</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.duration_hours,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.duration_hours
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :readonly="true" :disabled="true" :invalid="user_form_work_activity
                                                    .errors?.work_day?.note
                                                " v-model="user_form_work_activity
                                                        .work_day.note
                                                    " size="small" id="work_day_note" aria-describedby="work_day_note-help"
                                                fluid rows="5" cols="30" style="resize: none" />
                                            <label for="work_day_note">Detalles</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.work_day?.note,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.work_day?.note
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <FloatLabel>
                                            <Textarea :readonly="true" :disabled="true" :invalid="user_form_work_activity
                                                    .errors?.work_day?.note
                                                " v-model="user_form_work_activity
                                                        .work_day.note
                                                    " size="small" id="work_day_note" aria-describedby="work_day_note-help"
                                                fluid rows="5" cols="30" style="resize: none" />
                                            <label for="work_day_note">Notas</label>
                                        </FloatLabel>
                                        <Message :class="{
                                            hidden: !user_form_work_activity
                                                .errors?.work_day?.note,
                                        }" severity="error" variant="simple" size="small">{{
                                                user_form_work_activity.errors
                                                    ?.work_day?.note
                                            }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field m-4">
                                        <label for="over_work_day_billable">¿Es pagable el día?</label>
                                        <ToggleButton :readonly="true" :disabled="true" v-model="user_form_work_activity.work_day
                                                .billable
                                            " onIcon="pi pi-check" offIcon="pi pi-times"
                                            inputId="over_work_day_billable" size="large" class="w-full sm:w-40"
                                            aria-label="Confirmation" />
                                    </div>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>

                    <div class="flex flex-col gap-2" style="min-height: 100%; max-width: 100%">
                        <div class="flex justify-between gap-2 field">
                            <SelectButton :disabled="!event_locked" v-model="work_activity_status"
                                :options="work_activity_options" size="large" />
                            <ToggleButton :disabled="!event_locked" v-model="event_locked" onLabel="Locked" offLabel="Unlocked"
                                onIcon="pi pi-lock-open" offIcon="pi pi-lock" class="w-36"
                                aria-label="Do you confirm" />
                        </div>
                    </div>
                    <div v-if="work_activity.work_event.editable" class="flex justify-start mt-4 gap-2">
                        <Button label="Modificar" severity="success" icon="pi pi-save" iconPos="left"
                            @click="submitUpdateWorkActivity()" />
                        <Button label="Eliminar" severity="danger" icon="pi pi-trash" iconPos="left"
                            @click="confirmDelete($event)" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
