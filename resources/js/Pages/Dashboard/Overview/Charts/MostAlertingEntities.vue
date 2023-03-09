<script setup>
import { inject, ref, computed } from 'vue';
import Chart from '@/Components/Chart.vue';

const translate = inject('translate');

const props = defineProps({
    id: String,
    entities: Array,
    title: String,
    minEntities: {
        type: Number,
        default: 0,
    },
    maxEntities: {
        type: Number,
        default: 5,
    },
    colors: Object,
});

const entitiesCount = ref(props.entities.length);

const filteredEntities = computed(() => {
    return props.entities
        .sort((a, b) => b.alerts_count - a.alerts_count)
        .filter(entity => entity.alerts_count > 0);
});

const barChart = ref({
    id: props.id,
    type: 'bar',
    data: {
        labels: filteredEntities.value
            .map(entity => `${entity.code} - ${entity.name}`)
            .slice(props.minEntities, props.maxEntities),
        datasets: [
            {
                label: translate('Number of alerts'),
                data: filteredEntities.value
                    .map(entity => entity.alerts_count)
                    .slice(props.minEntities, props.maxEntities),
                backgroundColor: props.colors.bar,
            },
        ],
    },
});
</script>

<template>
    <div class="flex flex-col p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md hover:bg-blue-100">
        <h5 class="mb-4 text-md font-bold tracking-tight text-gray-900">
            {{ title }}
        </h5>
        <div v-if="entitiesCount === 0 || entities.filter(o => o.alerts_count > 0).length === 0"
            class="flex flex-grow flex-col items-center justify-center px-12 py-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-24 h-24 mb-2 text-gray-neutral-55">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 3l8.735 8.735m0 0a.374.374 0 11.53.53m-.53-.53l.53.53m0 0L21 21M14.652 9.348a3.75 3.75 0 010 5.304m2.121-7.425a6.75 6.75 0 010 9.546m2.121-11.667c3.808 3.807 3.808 9.98 0 13.788m-9.546-4.242a3.733 3.733 0 01-1.06-2.122m-1.061 4.243a6.75 6.75 0 01-1.625-6.929m-.496 9.05c-3.068-3.067-3.664-7.67-1.79-11.334M12 12h.008v.008H12V12z" />
            </svg>
            <h3 class="text-xl font-bold text-gray-neutral-55 mb-6">
                {{ __('No data') }}
            </h3>
        </div>
        <Chart v-else class="flex-grow" :id="barChart.id"
            :type="barChart.type"
            direction="horizontal"
            :data="barChart.data"
            :specific-options="{ indexAxis: 'y', scales: { y: { display: false } } }" width="full"
            :inner-horizontal-bar-text-color="colors.text" />
    </div>
</template>