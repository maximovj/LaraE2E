<script setup>
// >>>>>>>>> Importaciones
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';

import {
    FileUpload,
    Button,
    ProgressBar,
    Badge,
} from "primevue";

import { usePrimeVue } from 'primevue/config';

import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

import ConfirmPopup from 'primevue/confirmpopup';
import { useConfirm } from "primevue/useconfirm";

// INICIO de lógica de programación
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * Variables
const file = ref(null);
const isUploading = ref(false);

const onSelect = (event) => {
    file.value = event.files[0]; // Guarda el archivo seleccionado
};

const uploadFile = async () => {
    if (!file.value) return;

    isUploading.value = true;

    const formData = new FormData();
    formData.append('excelFile', file.value);

    try {
        router.post('/upload', formData, {
            preserveScroll: true,
            onSuccess: (res) => {
                console.log('Se subió correctamente:', res);
            },
            onError: (res) => {
                console.log('Hubo un error:', res);
            },
            onFinish: () => {
                isUploading.value = false;
            },
        });
    } catch (error) {
        console.error('Error al subir:', error);
        isUploading.value = false;
    }
};

const formatSize = (bytes) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;
    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    return `${size.toFixed(2)} ${units[unitIndex]}`;
};

// FIN de lógica de programación
// # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
</script>

<template>
    <Toast></Toast>
    <ConfirmPopup></ConfirmPopup>

    <Head title="Importar actividades"></Head>

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Importar actividades</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <FileUpload name="excelFile" @select="onSelect" accept=".xlsx,.xls" :maxFileSize="1_000_000"
                        :multiple="false" :auto="false" :customUpload="true">
                        <template #header="{ chooseCallback }">
                            <div class="flex gap-2">
                                <Button @click="chooseCallback()" icon="pi pi-file-excel" label="Seleccionar archivo"
                                    class="p-button-outlined" />
                                <Button @click="uploadFile" icon="pi pi-upload" label="Subir" severity="success"
                                    :disabled="!file" :loading="isUploading" />
                            </div>
                        </template>

                        <template #empty>
                            <div class="text-center py-4">
                                <i class="pi pi-cloud-upload text-5xl text-blue-500 mb-2" />
                                <p class="text-gray-600">Arrastra un archivo Excel (.xlsx, .xls)</p>
                            </div>
                        </template>

                        <template #content="{ files }">
                            <div v-if="files.length > 0" class="mt-4 p-3 border rounded">
                                <div class="flex items-center gap-3">
                                    <i class="pi pi-file-excel text-4xl text-green-500" />
                                    <div>
                                        <p class="font-medium">{{ files[0].name }}</p>
                                        <p class="text-sm text-gray-500">{{ formatSize(files[0].size) }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </FileUpload>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
