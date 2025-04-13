<script setup>
// Importaciones
import { ref, computed, watch, defineProps } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useAuthCache } from '@/composables/useAuthCache';

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
const filters = ref({ global: { value: null, matchMode: 'contains' } });
const loading = ref(false);

// Verificar permisos
const canCreate = computed(() => getCurrentPermissions().includes('employees.create'));
const canDelete = computed(() => getCurrentPermissions().includes('employees.delete'));
const canUpdate = computed(() => getCurrentPermissions().includes('employees.update'));
const canRead = computed(() => getCurrentPermissions().includes('employees.read'));

const onRowSelect = (event) => {
    if(canRead){
        //console.log('event: ', {event}, 'event.data.id: ' + event.data.id);
        router.visit(route('employees.show', event.data.id));
    }
}

const confirmDelete = (user) => {
  if (confirm('¿Estás seguro de eliminar este usuario?')) {
    router.delete(route('employees.destroy', user.id), {
      onSuccess: () => {
        localEmployees.value = localEmployees.value.filter(u => u.id !== user.id)
      }
    });
  }
}

// O si necesitas reaccionar a cambios
watch(() => props.employees, (newVal) => {
  localEmployees.value = [...newVal];
});

/*
console.log("Empleados (sin Proxy):", JSON.parse(JSON.stringify(localEmployees.value)));
console.log("Target:", localEmployees.value.__v_raw);
console.log("Employees Props: ", props.employees);
*/
</script>

<template>
    <Head title="Employees" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Employees</h2>
        </template>

        <div  class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between mb-6">
                        <h2 class="text-2xl font-semibold"></h2>
                        <Button
                        v-if="canCreate"
                        icon="pi pi-plus"
                        label="Nuevo Empleado"
                        />
                    </div>

                    <DataTable
                    :value="localEmployees"
                    tableStyle="min-width: 50rem"
                    :paginator="true"
                    :rows="50"
                    :rowsPerPageOptions="[50,100,500,1000]"
                    :loading="loading"
                    :filters="filters"
                    selectionMode="single"
                    @rowSelect="onRowSelect"
                    dataKey="employee_number"
                    stripedRows
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} empleados"
                    >

                    <template #header>
                    <div class="flex justify-between items-center">
                        <span class="p-input-icon-left">
                        <i class="pi pi-search me-2" />
                        <InputText v-model="filters.global.value" placeholder="Buscar..." />
                        </span>
                    </div>
                    </template>

                        <Column field="employee_number" header="No. de empleado"></Column>
                        <Column field="job_title" header="Título del trabajo"></Column>
                        <Column field="position" header="Posición"></Column>
                        <Column field="salary" header="Salario"></Column>
                        <Column field="shift" header="Horario"></Column>
                        <Column header="Estado">
                            <template #body="{data}">
                                <Tag :key="data.id" :value="data.status" class="mr-2" />
                            </template>
                        </Column>
                        <Column header="Acciones" v-if="canUpdate || canDelete">
                            <template #body="{data}">
                                <div class="flex space-x-2">
                                <Button
                                    v-if="canUpdate"
                                    icon="pi pi-pencil"
                                    class="p-button-rounded p-button-text"
                                     @click.stop="router.visit(route('employees.edit', data.id))"
                                />
                                <Button
                                    v-if="canDelete"
                                    icon="pi pi-trash"
                                    class="p-button-rounded p-button-text p-button-danger"
                                    @click.stop="confirmDelete(data)"
                                />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
