<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const user = computed(() => usePage().props.auth.user);
const currentRoles = computed(() => user.value?.roles || []);
const currentPermissions = computed(() => user.value?.permissions || []);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div v-role="['admin']"  class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-red-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">

                        <p class="text-slate-700">You're logged in Admin!</p>
                        <!-- Mostrar roles y permisos actuales -->
                        <p class="text-white">Roles: {{ currentRoles.join(', ') }}</p>
                        <p class="text-white">Permisos: {{ currentPermissions.join(', ') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div v-role="['user']"  class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
