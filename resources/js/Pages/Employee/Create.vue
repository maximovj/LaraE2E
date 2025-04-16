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
const { getCurrentPermissions } = useAuthCache();

// Verificar permisos
const canUsersCreate = computed(() => getCurrentPermissions().includes('users.create'));
const canUserProfilesCreate = computed(() => getCurrentPermissions().includes('user_profiles.create'));
const canEmployeesCrete = computed(() => getCurrentPermissions().includes('employees.delete'));

const props = defineProps({

});

const user_form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const user_profile_form = useForm({
    first_names: '',
    last_names: '',
    age: 18,
    birthdate: new Date(),
    marital_status: { name: 'single', code: 'single' },
    blood_type: '',
    address: '',
    zip_code: '',
    ssn: '',
    bank: '',
    interbank_clabe: '',
});

const employee_form = useForm({
    employee_number: '',
    job_title: '',
    position: '',
    hired_at: '',
    status: '',
    salary: '',
    shift: '',
    emergency_contact: '',
});

const toast = useToast();
const activeStep = ref(1);
const name = ref();
const email = ref();
const password = ref();
const option1 = ref(false);
const option2 = ref(false);
const option3 = ref(false);
const option4 = ref(false);
const option5 = ref(false);
const option6 = ref(false);
const option7 = ref(false);
const option8 = ref(false);
const option9 = ref(false);
const option10 = ref(false);

const marital_status = ref([
    { name: 'single', code: 'single' },
    { name: 'married', code: 'married' },
    { name: 'divorced', code: 'divorced' },
    { name: 'widowed', code: 'widowed' },
    { name: 'separated', code: 'separated' },
    { name: 'engaged', code: 'engaged' },
    { name: 'domestic_partnership', code: 'domestic_partnership' },
]);

const isNotErrors = (obj) => {
  return obj && typeof obj === 'object' && !Array.isArray(obj) && Object.keys(obj).length === 0;
}

const submitUserCreate = (nextStep) => {
    user_form
    .transform((data) => ({
        ...data,
        active_step: activeStep.value,
    }))
    .post(route('employees.store'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Registro de cuenta',
                detail: 'Todos los campos son correctos'
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

const submitUserProfileCreate = (nextStep) => {
    user_profile_form
    .transform((data) => ({
        ...data,
        active_step: activeStep.value,
    }))
    .post(route('employees.store'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Registro de perfil de usuario',
                detail: 'Todos los campos son correctos',
                life: 3000,
            });
            activeStep.value = nextStep;
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Registro de perfil de usuario',
                detail: 'Hay errores en los campos',
                life: 3000,
            });
        },
        onFinish: () => {
            if(isNotErrors(user_profile_form.errors)){
                console.log('No hay errores, felicidades');
            }else {
                console.log('errores:', user_profile_form.errors);
            }
        },
    });
}

const finishStep = (nextStep) => {
    toast.add({  severity: 'info', summary: 'Test', detail: 'Estoy haciendo pruebas'});
    console.log(user_form.errors.name);


    activeStep.value = nextStep;
};
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
                            <Step  v-if="canUserProfilesCreate" v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="2">
                                <div class="flex flex-row flex-auto gap-2 pl-2" v-bind="a11yAttrs.root">
                                    <button class="bg-transparent border-0 inline-flex flex-col gap-2"
                                        @click="activateCallback" v-bind="a11yAttrs.header">
                                        <span :class="[
                                            'rounded-full border-2 w-12 h-12 inline-flex items-center justify-center',
                                            {
                                                'bg-primary text-primary-contrast border-primary':
                                                (value < activeStep),
                                                'bg-slate-950 text-white':
                                                (activeStep === value || activeStep > value),
                                            }
                                        ]">

                                            <i class="pi pi-id-card" />
                                        </span>
                                    </button>
                                    <Divider />
                                </div>
                            </Step>
                            <Step  v-if="canUserProfilesCreate" v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="3">
                                <div class="flex flex-row flex-auto gap-2 pl-2" v-bind="a11yAttrs.root">
                                    <button class="bg-transparent border-0 inline-flex flex-col gap-2"
                                        @click="activateCallback" v-bind="a11yAttrs.header">
                                        <span :class="[
                                            'rounded-full border-2 w-12 h-12 inline-flex items-center justify-center',
                                            {
                                                'bg-primary text-primary-contrast border-primary':
                                                (value < activeStep),
                                                'bg-slate-950 text-white':
                                                (activeStep === value || activeStep > value),
                                            }
                                        ]">

                                            <i class="pi pi-briefcase" />
                                        </span>
                                    </button>
                                    <Divider />
                                </div>
                            </Step>
                            <Step v-slot="{ activateCallback, value, a11yAttrs }" asChild :value="4">
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
                                <div class="flex pt-6 justify-end gap-2">
                                    <Button label="Omitir" severity="secondary" icon="pi pi-arrow-right" iconPos="right"
                                        @click="activateCallback(2)" />
                                    <Button label="Siguiente" icon="pi pi-arrow-right" iconPos="right"
                                        @click="submitUserCreate(2)" />
                                </div>
                            </StepPanel>
                            <StepPanel v-if="canUserProfilesCreate" v-slot="{ activateCallback }" :value="2">
                                <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 20rem">
                                    <div class="text-center mt-4 mb-4 text-xl font-semibold">
                                        Crear perfil de usuario
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel>
                                            <InputText
                                            :invalid="user_profile_form.errors?.first_names"
                                            v-model="user_profile_form.first_names"
                                            size="small"
                                            id="name"
                                            aria-describedby="name-help"
                                            fluid />
                                            <label for="name">Nombre(s)</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.first_names}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.first_names }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel>
                                            <InputText
                                            :invalid="user_profile_form.errors?.last_names"
                                            v-model="user_profile_form.last_names"
                                            size="small"
                                            id="email"
                                            aria-describedby="email-help"
                                            fluid />
                                            <label for="email">Apellidos(s)</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.last_names}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.last_names }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputNumber
                                            :invalid="user_profile_form.errors?.age"
                                            v-model="user_profile_form.age"
                                            inputId="on_integeronly"
                                            :min="1"
                                            :max="100"
                                            showButtons
                                            :default-value="18"
                                            fluid />
                                            <label for="on_integeronly">Edad</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.age}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.age }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <DatePicker
                                            :invalid="user_profile_form.errors?.birthdate"
                                            v-model="user_profile_form.birthdate"
                                            showIcon
                                            iconDisplay="input"
                                            :defaultValue="new Date('2006/12/25')"
                                            inputId="on_birthdate"
                                            fluid />
                                            <label for="on_birthdate">Fecha de nacimiento</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.birthdate}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.birthdate }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <Select
                                            :invalid="user_profile_form.errors?.marital_status"
                                            v-model="user_profile_form.marital_status"
                                            inputId="on_marital_status"
                                            :options="marital_status"
                                            optionLabel="name"
                                            class="w-full" />
                                            <label for="on_marital_status">Estado civil</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.marital_status}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.marital_status }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputText
                                            :invalid="user_profile_form.errors?.blood_type"
                                            v-model="user_profile_form.blood_type"
                                            size="small"
                                            id="blood_type"
                                            aria-describedby="blood_type-help"
                                            fluid />
                                            <label for="blood_type">Tipo de sangre</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.blood_type}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.blood_type }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputText
                                            :invalid="user_profile_form.errors?.address"
                                            v-model="user_profile_form.address"
                                            size="small"
                                            id="address"
                                            aria-describedby="address-help"
                                            fluid />
                                            <label for="address">Dirección</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.address}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.address }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputText
                                            :invalid="user_profile_form.errors?.zip_code"
                                            v-model="user_profile_form.zip_code"
                                            size="small"
                                            id="zip_code"
                                            aria-describedby="zip_code-help"
                                            fluid />
                                            <label for="zip_code">Código Postal</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.zip_code}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.zip_code }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputText
                                            :invalid="user_profile_form.errors?.ssn"
                                            v-model="user_profile_form.ssn"
                                            size="small"
                                            id="ssn"
                                            aria-describedby="ssn-help"
                                            fluid />
                                            <label for="ssn">NSS</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.ssn}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.ssn }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputText
                                            :invalid="user_profile_form.errors?.bank"
                                            v-model="user_profile_form.bank"
                                            size="small"
                                            id="bank"
                                            aria-describedby="bank-help"
                                            fluid />
                                            <label for="bank">Banco (Nombre)</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.bank}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.bank }}</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputText
                                            :invalid="user_profile_form.errors?.interbank_clabe"
                                            v-model="user_profile_form.interbank_clabe"
                                            size="small"
                                            id="interbank_clabe"
                                            aria-describedby="interbank_clabe-help"
                                            fluid />
                                            <label for="interbank_clabe">CLABE (interbancaria)</label>
                                        </FloatLabel>
                                        <Message :class="{'hidden': !user_profile_form.errors?.interbank_clabe}" severity="error" variant="simple" size="small">{{ user_profile_form.errors?.interbank_clabe }}</Message>
                                    </div>
                                </div>
                                <div class="flex pt-6 justify-between">
                                    <Button label="Volver" severity="secondary" icon="pi pi-arrow-left"
                                        @click="activateCallback(1)" />

                                    <div>
                                        <Button class="me-2" label="Omitir" severity="secondary" icon="pi pi-arrow-right" iconPos="right"
                                        @click="activateCallback(3)" />
                                        <Button label="Siguiente" icon="pi pi-arrow-right" iconPos="right"
                                            @click="submitUserProfileCreate(3)" />
                                    </div>
                                </div>
                            </StepPanel>
                            <StepPanel v-slot="{ activateCallback }" :value="3">
                                <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 20rem">
                                    <div class="text-center mt-4 mb-4 text-xl font-semibold">
                                        Crear empleado
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel>
                                            <InputText
                                            :invalid="true"
                                            v-model="employee_form.employee_number"
                                            type="text"
                                            size="small"
                                            id="employee_number"
                                            aria-describedby="employee_number-help"
                                            fluid />
                                            <label for="employee_number">Número de empleado</label>
                                        </FloatLabel>
                                        <Message class="hidden" severity="error" variant="simple" size="small">Enter your name.</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <DatePicker
                                            :invalid="true"
                                            v-model="employee_form.hired_at"
                                            showIcon
                                            iconDisplay="input"
                                            :defaultValue="new Date('2006/12/25')"
                                            inputId="on_hired_at"
                                            fluid />
                                            <label for="on_hired_at">Fecha de contratación</label>
                                        </FloatLabel>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel>
                                            <InputText
                                            :invalid="true"
                                            v-model="employee_form.job_title"
                                            type="text"
                                            size="small"
                                            id="job_title"
                                            aria-describedby="job_title-help"
                                            fluid />
                                            <label for="job_title">Titulo de trabajo</label>
                                        </FloatLabel>
                                        <Message class="hidden" severity="error" variant="simple" size="small">Enter your email.</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="in">
                                            <InputText
                                            :invalid="true"
                                            v-model="employee_form.position"
                                            type="text"
                                            size="small"
                                            id="position"
                                            aria-describedby="position-help"
                                            fluid />
                                            <label for="in_position">Posición</label>
                                        </FloatLabel>
                                        <Message class="hidden" severity="error" variant="simple" size="small">Enter your password.</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="in">
                                            <InputText
                                            :invalid="true"
                                            v-model="employee_form.shift"
                                            type="text"
                                            size="small"
                                            id="in_shift"
                                            aria-describedby="shift-help"
                                            fluid />
                                            <label for="in_shift">Horario laboral</label>
                                        </FloatLabel>
                                        <Message class="hidden" severity="error" variant="simple" size="small">Enter your confirm password.</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="on">
                                            <InputNumber
                                            :invalid="true"
                                            v-model="employee_form.salary"
                                            inputId="on_salary"
                                            locale="en-US"
                                            :minFractionDigits="2"
                                            showButtons
                                            fluid />
                                            <label for="on_salary">Horario laboral</label>
                                        </FloatLabel>
                                        <Message class="hidden" severity="error" variant="simple" size="small">Enter your confirm password.</Message>
                                    </div>
                                    <div class="flex flex-col gap-2 field mb-4">
                                        <FloatLabel variant="in">
                                            <InputText
                                            :invalid="true"
                                            v-model="employee_form.emergency_contact"
                                            type="text"
                                            size="small"
                                            id="in_emergency_contact"
                                            aria-describedby="emergency_contact-help"
                                            fluid />
                                            <label for="in_emergency_contact">Contacto de emergencia</label>
                                        </FloatLabel>
                                        <Message class="hidden" severity="error" variant="simple" size="small">Enter your confirm password.</Message>
                                    </div>
                                </div>
                                <div class="flex pt-6 justify-between">
                                    <Button label="Volver" severity="secondary" icon="pi pi-arrow-left"
                                        @click="activateCallback(2)" />
                                    <Button label="Siguiente" icon="pi pi-arrow-right" iconPos="right"
                                        @click="finishStep(4)" />
                                </div>
                            </StepPanel>
                            <StepPanel v-slot="{ activateCallback }" :value="4">
                                <div class="flex flex-col gap-2 mx-auto" style="min-height: 16rem; max-width: 24rem">
                                    <div class="text-center mt-4 mb-4 text-xl font-semibold">
                                        Empleado registrado exitosamente
                                    </div>
                                    <div class="flex justify-center">
                                        <img alt="logo"
                                            src="https://primefaces.org/cdn/primevue/images/stepper/content.svg" />
                                    </div>
                                </div>
                                <div class="flex pt-6 justify-start">
                                    <Button label="Volver" severity="secondary" icon="pi pi-arrow-left"
                                        @click="activateCallback(3)" />
                                </div>
                            </StepPanel>
                        </StepPanels>
                    </Stepper>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
