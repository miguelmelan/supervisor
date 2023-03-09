<script setup>
import { ref, inject } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const abbr = inject('abbr');

const props = defineProps({
    orchestratorConnections: Array,
    automatedProcesses: Array,
    pendingAlertsCount: Number,
});

const orchestratorConnectionsCount = ref(props.orchestratorConnections.length);
const automatedProcessesCount = ref(props.automatedProcesses.length);
const pendingAlertsCount = ref(props.pendingAlertsCount);
</script>

<template>
    <div class="h-32 overflow-auto">
        <Link :href="route('configuration.orchestrator-connections.index')"
            class="flex items-stretch justify-between p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md hover:bg-blue-100">
            <span class="font-bold self-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-blue-50">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                </svg>
            </span>
            <div class="flex flex-col items-end justify-between">
                <span class="text-sm text-right text-gray-neutral-55">{{ __('UiPath Orchestrator connections') }}</span>
                <span class="text-xl text-blue-50 font-bold">{{ orchestratorConnectionsCount }}</span>
            </div>
        </Link>
    </div>
    <div class="h-32 overflow-auto">
        <Link :href="route('configuration.automated-processes.index')"
            class="flex items-stretch justify-between p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md hover:bg-blue-100">
            <span class="font-bold self-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-blue-50">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                </svg>
            </span>
            <div class="flex flex-col items-end justify-between">
                <span class="text-sm text-right text-gray-neutral-55">{{ __('Automated business processes') }}</span>
                <span class="text-xl text-blue-50 font-bold">{{ automatedProcessesCount }}</span>
            </div>
        </Link>
    </div>
    <div class="h-32 overflow-auto">
        <div
            class="flex items-stretch justify-between p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md hover:bg-blue-100">
            <span class="font-bold self-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-blue-50">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" />
                </svg>
            </span>
            <div class="flex flex-col items-end justify-between">
                <span class="text-sm text-right text-gray-neutral-55">{{ __('Pending alerts') }}</span>
                <span class="text-xl text-blue-50 font-bold">{{ abbr(pendingAlertsCount, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="h-32 overflow-auto">
        <div
            class="flex items-stretch justify-between p-4 w-full h-full bg-white rounded-lg border border-blue-200 shadow-md hover:bg-blue-100">
            <span class="font-bold self-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-blue-50">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </span>
            <div class="flex flex-col items-end justify-between">
                <span class="text-sm text-right text-gray-neutral-55">{{ __('Rule-based alert triggers') }}</span>
                <span class="text-xl text-blue-50 font-bold">0</span>
            </div>
        </div>
    </div>
</template>