<script setup>
// >>>>> Importaciones
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useAuthCache } from '@/composables/useAuthCache';
import { Knob } from 'primevue';
import Chart from 'primevue/chart';

import { ref, onMounted } from "vue";

// !! Variables
const { getCurrentRoles } = useAuthCache();

onMounted(() => {
    chartDataDoughnut.value = setChartDoughnutData();
    chartOptionsDouhgnut.value = setChartDoughnutOptions();

    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});

const chartDataDoughnut = ref();
const chartOptionsDouhgnut = ref(null);

const chartData = ref();
const chartOptions = ref();


const setChartDoughnutData = () => {
    const documentStyle = getComputedStyle(document.body);

    return {
        labels: ['Nomina'],
        datasets: [
            {
                data: [540],
                backgroundColor: [documentStyle.getPropertyValue('--p-cyan-500')],
                hoverBackgroundColor: [documentStyle.getPropertyValue('--p-cyan-400')]
            }
        ]
    };
};

const setChartDoughnutOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');

    return {
        plugins: {
            legend: {
                labels: {
                    cutout: '60%',
                    color: textColor
                }
            }
        }
    };
};

const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [
            {
                type: 'line',
                label: 'Dataset 1',
                borderColor: documentStyle.getPropertyValue('--p-orange-500'),
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                data: [50, 25, 12, 48, 56, 76, 42]
            },
            {
                type: 'bar',
                label: 'Dataset 2',
                backgroundColor: documentStyle.getPropertyValue('--p-gray-500'),
                data: [21, 84, 24, 75, 37, 65, 34],
                borderColor: 'white',
                borderWidth: 2
            },
            {
                type: 'bar',
                label: 'Dataset 3',
                backgroundColor: documentStyle.getPropertyValue('--p-cyan-500'),
                data: [41, 52, 24, 74, 23, 21, 32]
            }
        ]
    };
};
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.6,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
}

</script>

<template>

    <Head title="Panel Empleado" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel Empleado</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-4">
                    <div class="h-full bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-center">Nomina Acumulado</h2>
                        <div class="h-[14rem] p-4 bg-white rounded shadow overflow-hidden">
                            <Chart
                            type="doughnut"
                            :data="chartDataDoughnut"
                            :options="chartOptionsDouhgnut"
                            class="w-full max-w-[12rem] aspect-square mx-auto"
                            />
                        </div>
                    </div>

                    <div class="h-full bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-center">Actividades de trabajo</h2>
                        <div class="h-[14rem] p-4 bg-white rounded shadow overflow-hidden">
                            <Chart
                            type="doughnut"
                            :data="chartDataDoughnut"
                            :options="chartOptionsDouhgnut"
                            class="w-full max-w-[12rem] aspect-square mx-auto"
                            />
                        </div>
                    </div>

                    <div class="h-full bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-center">Horas de trabajo</h2>
                        <div class="h-[14rem] p-4 bg-white rounded shadow overflow-hidden">
                            <Chart
                            type="doughnut"
                            :data="chartDataDoughnut"
                            :options="chartOptionsDouhgnut"
                            class="w-full max-w-[12rem] aspect-square mx-auto"
                            />
                        </div>
                    </div>

                    <div class="h-full bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-center">Días trabajado</h2>
                        <div class="h-[14rem] p-4 bg-white rounded shadow overflow-hidden">
                            <Chart
                            type="doughnut"
                            :data="chartDataDoughnut"
                            :options="chartOptionsDouhgnut"
                            class="w-full max-w-[12rem] aspect-square mx-auto"
                            />
                        </div>
                    </div>
                </div>

                <div class="bg-white my-3 p-6 rounded-lg">
                    <Chart type="bar" :data="chartData" :options="chartOptions" class="h-[30rem]" />
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
