<script setup>
// >>>>>>>>> Importaciones
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch, defineProps } from 'vue';

import { Step, Stepper, StepList, StepPanel, StepPanels, FloatLabel, InputNumber, DatePicker, Select } from 'primevue';

import { FilterMatchMode } from '@primevue/core/api';
import { useConfirm } from "primevue/useconfirm";
import { useToast } from 'primevue/usetoast';
import ConfirmPopup from 'primevue/confirmpopup';
import Toast from 'primevue/toast';

import { useAuthCache } from '@/composables/useAuthCache';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// INICIO de lógica de programación
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// Obtener props
const props = defineProps({ });

const toast = useToast();
const activeStep = ref(1);
const { getCurrentPermissions } = useAuthCache();

// Verificar permisos
const canUsersCreate = computed(() => getCurrentPermissions().includes('users.create'));

// Crear Hook para formularios
const user_form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const isNotErrors = (obj) => {
  return obj && typeof obj === 'object' && !Array.isArray(obj) && Object.keys(obj).length === 0;
}

const submitUserCreate = (nextStep) => {
    user_form
    .transform((data) => ({
        ...data,
        active_step: activeStep.value, // value = 1
    }))
    .post(route('users.store'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Registro de cuenta',
                detail: 'Se registro una nueva cuenta correctamente'
            });
            activeStep.value = nextStep;
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Registro de cuenta',
                detail: 'Hay errores en los campos',
            });
        },
        onFinish: () => {
            if(isNotErrors(user_form.errors)){
                console.log('No hay errores, felicidades');
            }else {
                console.log('errores:', user_form.errors);
            }
        },
    });
}

// FIN de lógica de programación
// # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
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
                    <Stepper v-model:value="activeStep" class="basis-[40rem]">
                        <StepList>
                            <Step v-if="canUsersCreate" v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="1">
                                <div class="flex flex-row flex-auto gap-2" v-bind="a11yAttrs.root">
                                    <button class="bg-transparent border-0 inline-flex flex-col gap-2"
                                        @click="activateCallback" v-bind="a11yAttrs.header">
                                        <span :class="[
                                            'rounded-full border-2 w-12 h-12 inline-flex items-center justify-center bg-slate-950 text-white'
                                        ]">
                                            <i class="pi pi-user" />
                                        </span>
                                    </button>
                                    <Divider />
                                </div>
                            </Step>
                            <Step v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="2">
                                <div class="flex flex-row pl-2" v-bind="a11yAttrs.root">
                                    <button class="bg-transparent border-0 inline-flex flex-col gap-2"
                                        @click="activateCallback" v-bind="a11yAttrs.header">
                                        <span :class="[
                                            'rounded-full border-2 w-12 h-12 inline-flex items-center justify-center',
                                            {
                                                'bg-primary text-primary-contrast border-primary':
                                                (value < activeStep || value > activeStep),
                                                'bg-slate-950 text-white':
                                                value === activeStep
                                            }
                                        ]">
                                            <i class="pi pi-star" />
                                        </span>
                                    </button>
                                </div>
                            </Step>
                        </StepList>
                        <StepPanels>
                            <StepPanel v-if="canUsersCreate" v-slot="{ activateCallback }" :value="1">
                                <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 20rem">
                                    <div class="text-center mt-4 mb-4 text-xl font-semibold">
                                        Crear cuenta de usuario
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel>
                                            <InputText
                                            :invalid="user_form.errors?.name"
                                            v-model="user_form.name"
                                            size="small"
                                            id="name"
                                            aria-describedby="name-help"
                                            fluid />
                                            <label for="name">Nombre</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_form.errors?.name}" severity="error" variant="simple" size="small">{{ user_form.errors?.name }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel>
                                            <InputText
                                            :invalid="user_form.errors?.email"
                                            v-model="user_form.email"
                                            type="email"
                                            size="small"
                                            id="email"
                                            aria-describedby="email-help"
                                            fluid />
                                            <label for="email">Correo electrónico</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_form.errors?.email}" severity="error" variant="simple" size="small">{{ user_form.errors?.email }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="in">
                                            <Password
                                            :invalid="user_form.errors?.password"
                                            v-model="user_form.password"
                                            inputId="in_password"
                                            fluid />
                                            <label for="in_password">Contraseña</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_form.errors?.password}" severity="error" variant="simple" size="small">{{ user_form.errors?.password }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="in">
                                            <Password
                                            :invalid="user_form.errors?.password_confirmation"
                                            v-model="user_form.password_confirmation"
                                            inputId="in_password_confirmation"
                                            fluid />
                                            <label for="in_password_confirmation">Confirmar Contraseña</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_form.errors?.password_confirmation}" severity="error" variant="simple" size="small">{{ user_form.errors?.password_confirmation }}</Message>
                                    </div>
                                </div>
                                <div class="flex pt-6 justify-between gap-2">
                                    <Button label="Volver" severity="secondary" icon="pi pi-arrow-left" iconPos="left" />
                                    <Button label="Siguiente" icon="pi pi-arrow-right" iconPos="right"
                                        @click="submitUserCreate(2)" />
                                </div>
                            </StepPanel>
                            <StepPanel v-slot="{ activateCallback }" :value="2">
                                <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 24rem">
                                    <div class="text-center mt-4 mb-4 text-xl font-semibold">
                                        Cuenta registrado exitosamente
                                    </div>
                                    <div class="flex justify-center">
                                        <img alt="logo"
                                            src="https://primefaces.org/cdn/primevue/images/stepper/content.svg" />
                                    </div>
                                </div>
                                <div class="flex pt-6 justify-start">
                                    <Button label="Volver" severity="secondary" icon="pi pi-arrow-left"
                                        @click="activateCallback(1)" />
                                </div>
                            </StepPanel>
                        </StepPanels>
                    </Stepper>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
