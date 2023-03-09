<script setup>
import { ref, computed } from 'vue';
import Chart from '@/Components/Chart.vue';

const props = defineProps({
    orchestratorConnectionTenants: Array,
    chartConfiguration: Object,
});

const verifiedOrchestratorConnectionTenants = computed(() => props.orchestratorConnectionTenants.filter(connection => connection.verified));
const unverifiedOrchestratorConnectionTenants = computed(() => props.orchestratorConnectionTenants.filter(connection => !connection.verified && !connection.verified_at_for_humans));
const unreachableOrchestratorConnectionTenants = computed(() => props.orchestratorConnectionTenants.filter(connection => !connection.verified && connection.verified_at_for_humans));

const orchestratorConnectionTenantsStatusesDoughnutChart = ref({
    id: 'orchestrator-connection-tenants-statuses-chart',
    type: 'doughnut',
    data: {
        labels: props.chartConfiguration.labels.connectivityStatuses,
        datasets: [
            {
                data: [
                    verifiedOrchestratorConnectionTenants.value.length,
                    unverifiedOrchestratorConnectionTenants.value.length,
                    unreachableOrchestratorConnectionTenants.value.length,
                ],
                backgroundColor: props.chartConfiguration.colors.connectivityStatuses,
            },
        ],
    },
});
</script>

<template>
    <div
        class="flex flex-col p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md hover:bg-blue-100 col-span-2">
        <h5 class="mb-4 text-md font-bold tracking-tight text-gray-900">
            {{ __('UiPath Orchestrator connection tenants') }}
        </h5>
        <Chart v-if="orchestratorConnectionTenants.length > 0" class="flex-grow" :id="orchestratorConnectionTenantsStatusesDoughnutChart.id"
            :type="orchestratorConnectionTenantsStatusesDoughnutChart.type"
            :data="orchestratorConnectionTenantsStatusesDoughnutChart.data" />
        <div v-else class="flex flex-grow flex-col items-center justify-center">
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