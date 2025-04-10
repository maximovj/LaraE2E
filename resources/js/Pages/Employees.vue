<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useAuthCache } from '@/composables/useAuthCache';

const { hasRole, hasPermission } = useAuthCache();

const user = computed(() => usePage().props.auth.user);
const currentRoles = computed(() => user.value?.roles || []);
const currentPermissions = computed(() => user.value?.permissions || []);
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
                    <div class="p-6 text-gray-900">Show lists employees</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
