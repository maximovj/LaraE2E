<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useAuthCache } from '@/composables/useAuthCache';

const { getCurrentRoles } = useAuthCache();

const canAccessSuperAdmin = computed(() => getCurrentRoles().includes(['super-admin']));

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div   class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="canAccessSuperAdmin" class="p-6 text-white">
                        <p class="text-slate-700">You're logged in Admin!</p>
                    </div>
                    <div v-else class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
