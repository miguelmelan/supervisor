<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { Inertia } from '@inertiajs/inertia';
import ActionConfirmationModal from './ActionConfirmationModal.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    form: Object,
    mode: String,
    backLocation: String,
});

const singleAction = ref('');
const confirmingAction = ref(false);

const back = () => {
    Inertia.visit(props.backLocation, {
        preserveScroll: true,
    });
};

const triggerAction = (action) => {
    singleAction.value = action;
    confirmingAction.value = true;
};

const read = (callback) => {
    props.form.processing = true;
    Inertia.post(route('alerts.read', {
        alert: props.form.id,
    }), {}, {
        onSuccess: () => {
            props.form.processing = false;
            callback();
        },
        preserveScroll: true,
        preserveState: false,
    });
};

const lock = (callback) => {
    props.form.processing = true;
    Inertia.post(route('alerts.lock', {
        alert: props.form.id,
    }), {}, {
        onSuccess: () => {
            props.form.processing = false;
            callback();
        },
        preserveScroll: true,
        preserveState: false,
    });
};

const unlock = (callback) => {
    props.form.processing = true;
    Inertia.post(route('alerts.unlock', {
        alert: props.form.id,
    }), {}, {
        onSuccess: () => {
            props.form.processing = false;
            callback();
        },
        preserveScroll: true,
        preserveState: false,
    });
};

const singleDo = computed(() => {
    switch (singleAction.value) {
        case 'read':
            return read;
        case 'lock':
            return lock;
        case 'unlock':
            return unlock;
    }
});
</script>

<template>
    <SectionBorder />
    <div class="flex items-center justify-end">
        <PrimaryButton v-if="!form.read_at && form.locked_at && form.owned" @click.prevent="triggerAction('read')" class="mr-3">
            {{ __('Read') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </template>
        </PrimaryButton>
        <PrimaryButton v-if="!form.read_at && !form.locked_at" @click.prevent="triggerAction('lock')" class="mr-3">
            {{ __('Lock') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </template>
        </PrimaryButton>
        <PrimaryButton v-if="!form.read_at && form.locked_at && form.owned" @click.prevent="triggerAction('unlock')" class="mr-3">
            {{ __('Unlock') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </template>
        </PrimaryButton>
        <!-- <PrimaryButton @click.prevent="true" class="mr-3">
            {{ __('Save') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </template>
        </PrimaryButton> -->
        <SecondaryButton @click.prevent="back" as="button" @click="" class="mr-3">
            {{ __('Close') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>
        </SecondaryButton>
    </div>
    <ActionConfirmationModal :form="form" :action="singleAction" :do="singleDo" :show="confirmingAction" @close="confirmingAction = false" />
</template>