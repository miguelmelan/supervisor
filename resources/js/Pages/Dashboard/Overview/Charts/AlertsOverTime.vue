<script setup>
import { ref } from 'vue';
import Chart from '@/Components/Chart.vue';

const props = defineProps({
    alertsOverTime: Object,
});

const alertOverTimeMatrixChartCategories = [
    '00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
    '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00',
];

const alertsOverTimeHeatmap = ref({
    colors: [ '#f5222d' ],
    categories: alertOverTimeMatrixChartCategories,
    series: Object.entries(props.alertsOverTime).map((value, index) => {
        return {
            // value[0] is the date, value[1] is an object with all alerts per hour
            name: value[0],
            data: alertOverTimeMatrixChartCategories.map((category, categoryIndex) => {
                let y = Object.entries(value[1]).find(record => record[0] === category) ?? 0;
                if (y !== 0) {
                    y = y[1].length;
                }
                return { x: categoryIndex.toString(), y: y };
            }),
        };
    }),
    tooltip: {
        backgroundColor: 'bg-error-50',
    },
    height: '220px',
});
</script>

<template>
    <div
        class="flex flex-col p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md col-span-6">
        <h5 class="mb-4 text-md font-bold tracking-tight text-gray-900">
            {{ __('Number of alerts over time') }}
        </h5>
        <div v-if="Object.keys(alertsOverTime).length > 0" class="flex-grow">
            <Chart id="alerts-over-time-matrix-chart" type="heatmap" :heatmap="alertsOverTimeHeatmap" width="full" />
        </div>
        <div v-else class="flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-24 h-24 mb-2 text-gray-neutral-55">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 3l8.735 8.735m0 0a.374.374 0 11.53.53m-.53-.53l.53.53m0 0L21 21M14.652 9.348a3.75 3.75 0 010 5.304m2.121-7.425a6.75 6.75 0 010 9.546m2.121-11.667c3.808 3.807 3.808 9.98 0 13.788m-9.546-4.242a3.733 3.733 0 01-1.06-2.122m-1.061 4.243a6.75 6.75 0 01-1.625-6.929m-.496 9.05c-3.068-3.067-3.664-7.67-1.79-11.334M12 12h.008v.008H12V12z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-neutral-55 mb-6">
                {{ __('No data') }}
            </h3>
        </div>
    </div>
</template>