<script setup>
import { ref, inject, computed } from 'vue';
import Chart from '@/Components/Chart.vue';

const translate = inject('translate');

const props = defineProps({
    indicator: Object,
});

const count = computed(() => {
    const category = props.indicator.categories[props.indicator.categories.length - 1];
    return props.indicator.alerts[category] ? props.indicator.alerts[category].length : 0;
});
const progression = computed(() => {
    const previousAlerts = props.indicator.alerts[
        props.indicator.categories[props.indicator.categories.length - 2]
    ];
    const previousCount = previousAlerts ? previousAlerts.length : 0;
    return ((count.value - previousCount) / previousCount) * 100;
});
const barChart = ref({
    id: props.indicator.id,
    type: 'bar',
    data: {
        labels: props.indicator.categories,
        datasets: [
            {
                label: translate('Number of alerts'),
                data: props.indicator.categories.map(category => props.indicator.alerts[category] ? props.indicator.alerts[category].length : 0),
                backgroundColor: [ props.indicator.backgroundColor ],
            },
        ],
    },
});
</script>

<template>
    <div class="flex flex-col">
        <div
            class="flex items-stretch justify-between p-4 w-full h-32 bg-white rounded-t-lg border border-blue-200 shadow-md hover:bg-blue-100">
            <span class="font-bold self-center">
                <svg v-if="indicator.icon === 'clock'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-blue-50">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg v-if="indicator.icon === 'calendar'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-blue-50">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                </svg>
            </span>
            <div class="flex flex-col items-end justify-between">
                <span class="text-sm text-right text-gray-neutral-55">{{ indicator.title }}</span>
                <span class="text-xl text-blue-50 font-bold">
                    <span v-if="progression"
                        class="bg-error-50 text-white text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-1"
                        :class="{
                            'bg-error-50': progression > 0,
                            'bg-success-50': progression < 0,
                            'bg-gray-neutral-55': progression === 0,
                        }">
                        <svg v-if="progression === Infinity" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                        </svg>
                        <svg v-else-if="progression > 0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-1 w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                        <svg v-else-if="progression < 0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-1 w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" />
                        </svg>
                        <svg v-else-if="progression === 0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-1 w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                        </svg>

                        <span v-if="progression !== Infinity">
                            {{ Math.round(progression) }}%
                        </span>
                    </span>
                    {{ count }}
                </span>
            </div>
        </div>
        <div class="flex flex-grow flex-col justify-center p-4 w-full bg-white rounded-b-lg border border-blue-200 shadow-md hover:bg-blue-100">
            <Chart v-if="Object.keys(indicator.alerts).length > 0" :id="barChart.id" :type="barChart.type"
                :data="barChart.data" width="full" />
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
    </div>
</template>