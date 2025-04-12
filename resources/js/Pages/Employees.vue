<script setup>
// Importaciones
import { ref, computed, watch, defineProps } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useAuthCache } from '@/composables/useAuthCache';

// Lógica
const { hasRole, hasPermission } = useAuthCache();

const props = defineProps({
    employees: {
        type: Array,
        required: true,
        default: () => [] // Valor por defecto
    }
});

// Si necesitas manipular los datos
const localEmployees = ref([...props.employees]);
console.log("Empleados (sin Proxy):", JSON.parse(JSON.stringify(localEmployees.value)));
console.log("Target:", localEmployees.value.__v_raw);

// O si necesitas reaccionar a cambios
watch(() => props.employees, (newVal) => {
  localEmployees.value = [...newVal];
});
</script>

<template>
    <Head title="Employees" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Employees</h2>
        </template>

        <div  class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Button label="Agregar" />
                        <DataTable :value="employees" tableStyle="min-width: 50rem">
                            <Column field="nombre" header="nombre"></Column>
                            <Column field="apellido" header="apellido"></Column>
                            <Column field="edad" header="edad"></Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
