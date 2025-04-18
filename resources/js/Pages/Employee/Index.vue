<script setup>
// Importaciones
import { ref, computed, watch, defineProps } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useAuthCache } from '@/composables/useAuthCache';

import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from "primevue/useconfirm";

import { FilterMatchMode } from '@primevue/core/api';

// Lógica
const { getCurrentPermissions } = useAuthCache();

const props = defineProps({
    employees: {
        type: Array,
        required: true,
        default: () => [] // Valor por defecto
    }
});

// Si necesitas manipular los datos
const localEmployees = ref([...props.employees]);
const loading = ref(false);
const toast = useToast();
const confirm = useConfirm();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    status: { value: null, matchMode: FilterMatchMode.EQUALS }
});

const statuses = ref(['active', 'on_leave', 'fired']);
const getSeverity = (status) => {
    switch (status) {
        case 'active':
            return 'success';

        case 'on_leave':
            return 'info';

        case 'fired':
            return 'danger';

        default:
            return null;
    }
}

// Verificar permisos
const canCreate = computed(() => getCurrentPermissions().includes('employees.create'));
const canDelete = computed(() => getCurrentPermissions().includes('employees.delete'));
const canUpdate = computed(() => getCurrentPermissions().includes('employees.update'));
const canRead = computed(() => getCurrentPermissions().includes('employees.read'));

const onRowSelect = (event) => {
    if (canRead) {
        //console.log('event: ', {event}, 'event.data.id: ' + event.data.id);
        toast.add({ severity: 'info', summary: 'Empleado', detail: `${event.data.employee_number} | ${event.data.position}`, life: 3000 });
        //router.visit(route('employees.show', event.data.id));
    }
}

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
            toast.add({ severity: 'success', summary: 'Confirmado', detail: 'Acción aceptada correctamente', life: 3000 });
            router.delete(route('employees.destroy', user.id), {
                onSuccess: () => {
                    localEmployees.value = localEmployees.value.filter(u => u.id !== user.id)
                }
            });
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Cancela', detail: 'Acción cancelada correctamente', life: 3000 });
        }
    });
}



// O si necesitas reaccionar a cambios
watch(() => props.employees, (newVal) => {
    localEmployees.value = [...newVal];
});

console.log("Empleados (sin Proxy):", JSON.parse(JSON.stringify(localEmployees.value)));
console.log("Target:", localEmployees.value.__v_raw);
console.log("Employees Props: ", props.employees);
</script>

<template>
    <Toast />
    <ConfirmPopup></ConfirmPopup>

    <Head title="Employees" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Employees</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-2xl font-semibold"></h2>
                        <Button
                        v-if="canCreate"
                        icon="pi pi-plus"
                        label="Nuevo Empleado"
                        @click="router.visit(route('employees.create'))"
                        />
                    </div>

                    <DataTable
                        :value="localEmployees"
                        tableStyle="min-width: 50rem"
                        :rows="50"
                        :rowsPerPageOptions="[50, 100, 500, 1000]"
                        :loading="loading"
                        v-model:filters="filters"
                        filterDisplay="menu"
                        selectionMode="single"
                        @rowSelect="onRowSelect"
                        dataKey="employee_number"
                        stripedRows
                        showGridlines
                        removableSort
                        sortMode="multiple"
                        :paginator="true"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} empleados">

                        <template #header>
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xl font-bold">Empleados</span>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="p-input-icon-left">
                                        <i class="pi pi-search me-2" />
                                        <InputText v-model="filters.global.value" placeholder="Buscar..." />
                                    </span>
                                    <Button icon="pi pi-refresh" rounded raised />
                                </div>
                            </div>
                        </template>

                        <Column field="employee_number" header="No. de empleado" sortable></Column>
                        <Column field="job_title" header="Título del trabajo" sortable></Column>
                        <Column field="position" header="Posición" sortable></Column>
                        <Column field="salary" header="Salario" sortable></Column>
                        <Column field="shift" header="Horario" sortable></Column>
                        <Column header="Estado" field="status" :filterMenuStyle="{ width: '2rem' }"
                            style="min-width: 2rem">
                            <template #body="{ data }">
                                <Tag :value="data.status" :severity="getSeverity(data.status)" />
                            </template>
                            <template #filter="{ filterModel }">
                                <Select v-model="filterModel.value" :options="statuses" size="small" placeholder="Select One"
                                    showClear>
                                    <template #option="slotProps">
                                        <Tag :value="slotProps.option" :severity="getSeverity(slotProps.option)" />
                                    </template>
                                </Select>
                            </template>
                        </Column>
                        <Column header="Acciones" v-if="canUpdate || canDelete">
                            <template #body="{ data }">
                                <div class="flex space-x-2">
                                    <Button v-if="data?.user" severity="success" icon="pi pi-user"
                                        class="p-button-rounded"
                                        @click.stop="router.visit(route('employees.user', data.employee_number))" />
                                    <Button v-else severity="help" icon="pi pi-user-plus"
                                        class="p-button-rounded p-button-text"
                                        @click.stop="router.visit(route('employees.user', data.employee_number))" />
                                    <Button v-if="data.user?.user_profile" severity="success" icon="pi pi-id-card"
                                        class="p-button-rounded" />
                                    <Button v-else icon="pi pi-id-card" severity="help" class="p-button-rounded p-button-text" />
                                    <Button v-if="canUpdate" icon="pi pi-pencil" class="p-button-rounded p-button-text"
                                        @click.stop="router.visit(route('employees.edit', data.id))" />
                                    <Button v-if="canDelete" icon="pi pi-trash"
                                        class="p-button-rounded p-button-text p-button-danger" severity="danger"
                                        @click="confirmDelete($event, data)" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
