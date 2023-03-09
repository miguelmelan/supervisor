<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    automatedProcesses: Array,
});

const automatedProcessesCount = ref(props.automatedProcesses.length);

const automatedProcessesHealth = computed(() => 
    Math.round(props.automatedProcesses.map(process => process.health).reduce((prev, curr) => prev + curr, 0) / props.automatedProcesses.length * 100)
);
</script>

<template>
    <div class="flex flex-col p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md hover:bg-blue-100">
        <h5 class="mb-4 text-md font-bold tracking-tight text-gray-900">
            {{ __('Automated business processes health') }}
        </h5>
        <div class="flex flex-grow flex-col items-center justify-center px-12 py-8 text-center">
            <svg v-if="automatedProcessesCount > 0 && automatedProcessesHealth < 70" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-48 h-48 mb-2"
                :class="{
                    'text-error-50': automatedProcessesHealth < 30,
                    'text-warning-50': automatedProcessesHealth >= 30,
                }">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
            </svg>
            <svg v-else-if="automatedProcessesCount > 0 && automatedProcessesHealth >= 70"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-48 h-48 mb-2 text-success-50">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-24 h-24 mb-2 text-gray-neutral-55">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 3l8.735 8.735m0 0a.374.374 0 11.53.53m-.53-.53l.53.53m0 0L21 21M14.652 9.348a3.75 3.75 0 010 5.304m2.121-7.425a6.75 6.75 0 010 9.546m2.121-11.667c3.808 3.807 3.808 9.98 0 13.788m-9.546-4.242a3.733 3.733 0 01-1.06-2.122m-1.061 4.243a6.75 6.75 0 01-1.625-6.929m-.496 9.05c-3.068-3.067-3.664-7.67-1.79-11.334M12 12h.008v.008H12V12z" />
            </svg>

            <h6 v-if="automatedProcessesCount > 0" class="text-4xl font-bold sm:text-5xl mb-6"
                :class="{
                    'text-error-50': automatedProcessesHealth < 30,
                    'text-warning-50': automatedProcessesHealth >= 30 && automatedProcessesHealth < 70,
                    'text-success-50': automatedProcessesHealth >= 70,
                }">
                {{ automatedProcessesHealth }}%
            </h6>
            <h3 v-else class="text-xl font-bold text-gray-neutral-55 mb-6">
                {{ __('No data') }}
            </h3>
            <p v-if="automatedProcessesCount > 0" class="text-center text-xs text-gray-neutral-55">
                {{ __('This indicator helps to evaluate how much of your processes are currently affected by an alert.') }}
            </p>
        </div>
    </div>
</template>