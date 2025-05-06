<template>
    <div class="card flex justify-center">
        <IftaLabel>
            <DatePicker
            ref="DatePickerRef"
            size="small"
            inputId="date"
            v-model="currentDate"
            iconDisplay="input"
            hourFormat="24"
            variant="outlined"
            disabled
            readonly
            showSeconds
            showIcon
            showTime
            fluid
            />
            <label for="date">Fecha y Hora</label>
        </IftaLabel>
    </div>
</template>

<script setup>
import { IftaLabel, DatePicker } from 'primevue';
import { ref, onMounted, onBeforeUnmount } from 'vue';

// Datos reactivos
const currentDate = ref(new Date());
const currentTime = ref('');

// Función para actualizar la hora
const updateTime = () => {
    currentDate.value = new Date();
    currentTime.value = new Date().toLocaleTimeString();
    //console.log('reloj:', currentDate.value, currentTime.value);
};

// Temporizador (se inicia al montar el componente)
let interval;
onMounted(() => {
  updateTime();
  interval = setInterval(updateTime, 1000);
});

// Limpieza del temporizador (al desmontar el componente)
onBeforeUnmount(() => {
  clearInterval(interval);
});
</script>
