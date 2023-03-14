<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed } from 'vue';

const props = defineProps({
    form: Object,
    action: String,
    do: Function,
    show: Boolean,
});

const emit = defineEmits(['close']);

const show = computed(() => props.show);

const job = () => {
    props.do(close);
};

const close = () => {
    emit('close');
};
</script>

<template>
    <ConfirmationModal :show="show" @close="close" :closeable="!form.processing" severity="info">
        <template #title>
            {{ __(`${action.charAt(0).toUpperCase() + action.slice(1)} a pending alert`) }}
        </template>

        <template #content>
            {{ __(`Are you sure you want to ${action} this pending alert?`) }}
        </template>

        <template #footer>
            <SecondaryButton @click="close" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                {{ __('Cancel') }}
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="job">
                {{ __(action.charAt(0).toUpperCase() + action.slice(1)) }}
                <template #icon>
                    <svg v-if="action === 'read'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <svg v-if="action === 'lock'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <svg v-if="action === 'unlock'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </template>
            </PrimaryButton>
        </template>
    </ConfirmationModal>
</template>