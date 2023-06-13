<script setup>
import { onMounted, onUnmounted, ref, inject } from 'vue';
import { Head } from '@inertiajs/inertia-vue3';
import Banner from '@/Components/Banner.vue';
import FlashToast from '@/Components/FlashToast.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Footer from '@/Components/Footer.vue';
import Navbar from './Navbar/Index.vue';

const translate = inject('translate');
const sendNotification = inject('sendNotification');

defineProps({
    title: String,
});

const showNotificationPermissionBar = ref(Notification.permission === 'default');

const requestNotificationPermission = () => {
    Notification.requestPermission().then(status => {
        showNotificationPermissionBar.value = false;
        if (status === 'granted') {
            location.reload();
        }
    });
};

let channel = null;

onMounted(() => {
    document.querySelectorAll('[data-tooltip-target^="tooltip"]').forEach(trigger => {
        const target = document.getElementById(trigger.getAttribute('data-tooltip-target'));
        new Tooltip(target, trigger, {
            placement: target.getAttribute('data-position') ?? 'bottom',
        });
    });

    channel = Echo.channel('orchestrator-connection-tenant-alert')
        .listen('.new', (data) => {
            const alert = data.resource;
            let body = translate('A new alert has been created!');
            body += ` (#${alert.id_padded})`;
            if (alert.trigger) {
                body += `\n${translate('Trigger name: :name', { name: alert.trigger.name })}`;
            }
            sendNotification(body);
        });
});

onUnmounted(() => {
    channel.stopListening('.new');
});
</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <div class="min-h-screen flex flex-col justify-between bg-gray-cold-15">

            <Navbar />

            <!-- Notification permission request -->
            <div v-if="showNotificationPermissionBar" class="bg-blue-100 p-4 flex items-center justify-center">
                <span class="mr-2">{{ __('Want to get notifications from us?') }}</span>
                <PrimaryButton @click="requestNotificationPermission" as="button">
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59" />
                        </svg>
                    </template>
                    {{ __('Set permissions') }}
                </PrimaryButton>
            </div>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-end">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-grow">
                <div class="py-12">
                    <div class="w-full sm:px-6 lg:px-8">
                        <slot />
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <Footer></Footer>

            <FlashToast />
        </div>
    </div>
</template>
