<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import { ref } from 'vue';

const props = defineProps({
    form: Object,
    handler: Function,
});

const emit = defineEmits(['close']);

const cancelling = ref(true);

const close = () => {
    cancelling.value = false;
    emit('close');
};
</script>

<template>
    <ConfirmationModal :show="cancelling" @close="close" :closeable="!form.processing">
        <template #title>
            {{ __('Unsaved changes') }}
        </template>

        <template #content>
            {{ __('Do you want to leave this page without saving your latest changes?') }}
        </template>

        <template #footer>
            <SecondaryButton @click="close"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                {{ __('Stay on page') }}
            </SecondaryButton>

            <PrimaryButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="handler">
                {{ __('Leave page') }}
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </template>
            </PrimaryButton>
        </template>
    </ConfirmationModal>
</template>