<script setup>
// >>>>>>>>> Importaciones
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

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
const $primevue = usePrimeVue();
const toast = useToast();

const totalSize = ref(0);
const totalSizePercent = ref(0);
const files = ref([]);
const urlFileUpload = computed(() => router.post(route('work-activities.import')));

// * Métodos / Funciones Flechas
const onRemoveTemplatingFile = (file, removeFileCallback, index) => {
    removeFileCallback(index);
    totalSize.value = 0;
    totalSizePercent.value = 0;
};

const onClearTemplatingUpload = (clear) => {
    clear();
    totalSize.value = 0;
    totalSizePercent.value = 0;
};

const onSelectedFiles = (event) => {
    // Solo mantener el primer archivo si por alguna razón se seleccionan múltiples
    files.value = event.files.slice(0, 1);
    totalSize.value = files.value[0] ? parseInt(formatSize(files.value[0].size)) : 0;
};

const uploadEvent = (callback) => {
    totalSizePercent.value = totalSize.value / 10;
    callback();
};

const onTemplatedUpload = () => {
    toast.add({ severity: "info", summary: "Success", detail: "File Uploaded", life: 3000 });
};

const formatSize = (bytes) => {
    const k = 1024;
    const dm = 3;
    const sizes = $primevue.config.locale.fileSizeTypes;

    if (bytes === 0) {
        return `0 ${sizes[0]}`;
    }

    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

    return `${formattedSize} ${sizes[i]}`;
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
                    <FileUpload name="demo[]" url="/api/upload" @upload="onTemplatedUpload($event)" :multiple="true"
                        accept="image/*" :maxFileSize="1000000" @select="onSelectedFiles">
                        <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                            <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                                <div class="flex gap-2">
                                    <Button @click="chooseCallback()" icon="pi pi-images" rounded outlined
                                        severity="secondary"></Button>
                                    <Button @click="uploadEvent(uploadCallback)" icon="pi pi-cloud-upload" rounded
                                        outlined severity="success" :disabled="!files || files.length === 0"></Button>
                                    <Button @click="clearCallback()" icon="pi pi-times" rounded outlined
                                        severity="danger" :disabled="!files || files.length === 0"></Button>
                                </div>
                                <ProgressBar :value="totalSizePercent" :showValue="false"
                                    class="md:w-20rem h-1 w-full md:ml-auto">
                                    <span class="whitespace-nowrap">{{ totalSize }}B / 1Mb</span>
                                </ProgressBar>
                            </div>
                        </template>
                        <template #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
                            <div class="flex flex-col gap-8 pt-4">
                                <div v-if="files.length > 0">
                                    <h5>Pending</h5>
                                    <div class="flex flex-wrap gap-4">
                                        <div v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                            class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                            <div>
                                                <img role="presentation" :alt="file.name" :src="file.objectURL"
                                                    width="100" height="50" />
                                            </div>
                                            <span
                                                class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{
                                                file.name }}</span>
                                            <div>{{ formatSize(file.size) }}</div>
                                            <Badge value="Pending" severity="warn" />
                                            <Button icon="pi pi-times"
                                                @click="onRemoveTemplatingFile(file, removeFileCallback, index)"
                                                outlined rounded severity="danger" />
                                        </div>
                                    </div>
                                </div>

                                <div v-if="uploadedFiles.length > 0">
                                    <h5>Completed</h5>
                                    <div class="flex flex-wrap gap-4">
                                        <div v-for="(file, index) of uploadedFiles"
                                            :key="file.name + file.type + file.size"
                                            class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                            <div>
                                                <img role="presentation" :alt="file.name" :src="file.objectURL"
                                                    width="100" height="50" />
                                            </div>
                                            <span
                                                class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{
                                                file.name }}</span>
                                            <div>{{ formatSize(file.size) }}</div>
                                            <Badge value="Completed" class="mt-4" severity="success" />
                                            <Button icon="pi pi-times" @click="removeUploadedFileCallback(index)"
                                                outlined rounded severity="danger" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template #empty>
                            <div class="flex items-center justify-center flex-col">
                                <i
                                    class="pi pi-cloud-upload !border-2 !rounded-full !p-8 !text-4xl !text-muted-color" />
                                <p class="mt-6 mb-0">Drag and drop files to here to upload.</p>
                            </div>
                        </template>
                    </FileUpload>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
