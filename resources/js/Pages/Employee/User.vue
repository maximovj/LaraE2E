<script setup>
// >>>>>>>>> Importaciones
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch, defineProps } from 'vue';

import { Step, Stepper, StepList, StepPanel, StepPanels, FloatLabel, InputNumber, DatePicker, Select, MultiSelect } from 'primevue';

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
const props = defineProps({
    action: {
        type: String,
        default: 'users.create',
    },
    employee: {
        type: Object,
        default: null,
    },
    user: {
        type: Object,
        default: null,
    },
});

//const inertia_session = computed(() => usePage().props.inertia_session);
const initRoles = computed(() => {
  return props.user?.roles?.map(el => ({ name: el.name, code: el.name })) || []
});

const selectedRoles = ref(initRoles.value);
const toast = useToast();
const activeStep = ref(1);
const userRef = ref(props.user);
const { getCurrentPermissions } = useAuthCache();

// Verificar permisos
const canUsersUpdate = computed(() => getCurrentPermissions().includes('users.update'));
const canUsersCreate = computed(() => getCurrentPermissions().includes('users.create'));

// Crear Hook para formularios
const user_form = useForm({
    name: props.user?.name,
    email: props.user?.email,
    password: '',
    password_confirmation: '',
});

const roles = ref([
    { name: 'company-admin', code: 'company-admin' },
    { name: 'office-manager', code: 'office-manager' },
    { name: 'regular-user', code: 'regular-user' },
]);

const isNotErrors = (obj) => {
  return obj && typeof obj === 'object' && !Array.isArray(obj) && Object.keys(obj).length === 0;
}

const submitUserCreate = (nextStep) => {
    user_form
    .transform((data) => ({
        ...data,
        roles: selectedRoles.value,
        active_step: activeStep.value, // value = 1
    }))
    .post(route('users.store'), {
        headers: {
            'X-Inertia' : true,
        },
        preserveState: true,
        onSuccess: (res) => {
            toast.add({
                severity: 'success',
                summary: 'Registro de cuenta',
                detail: 'Se registro una nueva cuenta correctamente'
            });

            let new_user = res?.props?.inertia_session?.data?.user;
            userRef.value = new_user;
            user_form.reset();
            submitEmployeeUpdate(nextStep, new_user);
            //router.visit(route('employees.index'));
            //activeStep.value = nextStep;
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Registro de cuenta',
                detail: 'Hay errores en los campos',
            });
        }
    });
}

const submitUserUpdate = (nextStep) => {
    user_form
    .transform((data) => ({
        ...data,
        roles: selectedRoles.value,
        active_step: activeStep.value, // value = 1
    }))
    .patch(route('users.update', props.user), {
        headers: {
            'X-Inertia' : true,
        },
        preserveState: true,
        onSuccess: (res) => {
            activeStep.value = nextStep;
            toast.add({
                severity: 'success',
                summary: 'Modificar cuenta',
                detail: 'Se modifica cuenta correctamente'
            });

            let new_user = res?.props?.inertia_session?.data?.user;
            userRef.value = new_user;
            user_form.reset();
            submitEmployeeUpdate(nextStep, new_user);

            //router.visit(route('employees.index'));
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Modificar cuenta',
                detail: 'Hay errores en los campos',
            });
        }
    });
}

const submitEmployeeUpdate = (nextStep, new_user) => {
    router.put(route('employees.update', props.employee), {
        user: userRef.value,
    });
}

watch(userRef, (newValue, oldValue) => {
  user_form.email = newValue?.email;
  user_form.name = newValue?.name;
});

// FIN de lógica de programación
// # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
</script>

<template>
    <Toast />
    <ConfirmPopup></ConfirmPopup>

    <Head title="Cuenta de usuario" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cuenta de usuario</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <Stepper v-model:value="activeStep" class="basis-[40rem]">
                        <StepList>
                            <Step v-slot="{ value, a11yAttrs }" asChild :value="1">
                                <div class="flex flex-row flex-auto gap-2" v-bind="a11yAttrs.root">
                                    <button
                                        class="bg-transparent border-0 inline-flex flex-col gap-2">
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
                                    <button
                                        class="bg-transparent border-0 inline-flex flex-col gap-2">
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
                            <StepPanel v-slot="{ activateCallback }" :value="1">
                                <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 20rem">
                                    <div class="text-center mt-4 mb-4 text-xl font-semibold">
                                        {{ !user ? 'Crear cuenta de usuario' : 'Cuenta de usuario'  }}
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel>
                                            <InputText
                                            :invalid="!!user_form.errors?.name"
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
                                            :invalid="!!user_form.errors?.email"
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
                                            :invalid="!!user_form.errors?.password"
                                            v-model="user_form.password"
                                            toggleMask
                                            inputId="in_password"
                                            fluid />
                                            <label for="in_password">Contraseña</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_form.errors?.password}" severity="error" variant="simple" size="small">{{ user_form.errors?.password }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="in">
                                            <Password
                                            :invalid="!!user_form.errors?.password_confirmation"
                                            v-model="user_form.password_confirmation"
                                            toggleMask
                                            inputId="in_password_confirmation"
                                            fluid />
                                            <label for="in_password_confirmation">Confirmar Contraseña</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_form.errors?.password_confirmation}" severity="error" variant="simple" size="small">{{ user_form.errors?.password_confirmation }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel class="w-full md:w-80" variant="on">
                                            <MultiSelect id="on_roles"
                                            :invalid="!!user_form.errors?.roles"
                                            v-model="selectedRoles"
                                            :options="roles"
                                            optionLabel="name"
                                            :maxSelectedLabels="1"
                                            variant="filled"
                                            filter
                                            class="w-full md:w-80" />
                                            <label for="on_roles">Rol</label>
                                            <Message :class="{'hidden': !user_form.errors?.roles}" severity="error" variant="simple" size="small">{{ user_form.errors?.roles }}</Message>
                                        </FloatLabel>
                                    </div>
                                </div>
                                <div class="flex pt-6 justify-between gap-2">
                                    <Button label="Volver" severity="secondary" icon="pi pi-arrow-left" iconPos="left"
                                    @click.stop="router.visit(route('employees.index'))"
                                    />
                                    <Button v-if="canUsersCreate && !user" :label="!user ? 'Crear' : 'Modificar'" icon="pi pi-arrow-right" iconPos="right"
                                        @click="submitUserCreate(2)" />
                                    <Button v-if="canUsersUpdate && user" :label="!user ? 'Crear' : 'Modificar'" icon="pi pi-arrow-right" iconPos="right"
                                        @click="submitUserUpdate(2)" />
                                </div>
                            </StepPanel>
                            <StepPanel v-slot="{ activateCallback }" :value="2">
                                <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 24rem">
                                    <div class="text-center mt-4 mb-4 text-xl font-semibold">
                                        {{  !user ? 'Cuenta registrado exitosamente' : 'Cuenta modificado exitosamente'  }}
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
