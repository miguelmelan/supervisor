<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';
import Navbar from '../Navbar.vue';

const props = defineProps({
    closedAlerts: Array,
    pendingAlertsCount: Number,
});

const pendingAlertsCount = ref(props.pendingAlertsCount);
const newPendingAlertsCount = ref(0);
const closedAlertsCount = ref(props.closedAlerts.length);
const newClosedAlertsCount = ref(0);

onMounted(() => {
    Echo.channel('orchestrator-connection-tenant-alert')
        .listen('.new', (data) => {
            console.log(data);
            pendingAlertsCount.value++;
            newPendingAlertsCount.value++;
        });
    Echo.channel('orchestrator-connection-tenant-alert')
        .listen('.closed', (data) => {
            console.log(data);
            pendingAlertsCount.value--;
            newPendingAlertsCount.value--;
            closedAlertsCount.value++;
            newClosedAlertsCount.value++;
        });
});
</script>

<template>
    <AppLayout :title="__('Dashboard') + ' > ' + __('Closed alerts')">
        <div class="bg-white shadow-xl sm:rounded-lg">
            <PageContentHeader :text="__('Supervise your digital workforce')">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 h-10 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                    </svg>
                </template>
            </PageContentHeader>

            <!-- Navbar -->
            <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                <Navbar :pending-alerts-count="pendingAlertsCount"
                    :closed-alerts-count="closedAlertsCount" />
            </div>

            <!-- Main content -->
            <div class="px-6 pb-6 sm:px-20 bg-gray-200 bg-opacity-25">
                Soon ...
            </div>
        </div>
    </AppLayout>
</template>