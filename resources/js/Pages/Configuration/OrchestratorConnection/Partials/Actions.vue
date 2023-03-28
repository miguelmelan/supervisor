<script setup>
import { computed, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import DeleteConfirmationModal from './DeleteConfirmationModal.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import UnsavedChangesConfirmationModal from '@/Components/UnsavedChangesConfirmationModal.vue';

const props = defineProps({
    form: Object,
    mode: String,
    cancelling: Boolean,
    verification: Object,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});

const confirmationButtonText = computed(() => {
    let text = '';
    if (props.mode == 'create') {
        text = 'Connect with a UiPath Orchestrator';
    } else if (props.mode == 'edit') {
        text = 'Save';
    }
    return text;
});

const confirmationButtonIcon = computed(() => {
    let icon = '';
    if (props.mode == 'create') {
        icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>';
    } else if (props.mode == 'edit') {
        icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
    }
    return icon;
});

const confirmingDeletion = ref(false);

const goToIndexPage = () => {
    Inertia.get(route('configuration.orchestrator-connections.index'));
};
</script>

<template>
    <SectionBorder v-if="withTopBorder" />
    <ActionMessage v-if="mode === 'edit'" :on="form.recentlySuccessful" class="text-right" :class="{ 'mb-4': form.recentlySuccessful }">
        {{ __('Saved.') }}
    </ActionMessage>
    <ActionMessage :on="form.hasErrors" class="text-right" :class="{ 'mb-4': form.hasErrors }">
        {{ __('There are errors on the form. Please fix them before continuing.') }}
    </ActionMessage>
    <div class="flex items-center justify-end">
        <SecondaryButton as="button" @click="form.isDirty ? cancelling = true : goToIndexPage()" class="mr-3"
            :class="{ 'opacity-25': form.processing || verification.processing }"
            :disabled="form.processing || verification.processing">
            {{ __('Close') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>
        </SecondaryButton>
        <DangerButton v-if="mode == 'edit'" as="button" @click="confirmingDeletion = true" class="mr-3"
            :class="{ 'opacity-25': form.processing || verification.processing }"
            :disabled="form.processing || verification.processing">
            {{ __('Delete') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </template>
        </DangerButton>
        <PrimaryButton :class="{ 'opacity-25': form.processing || verification.processing || !form.isDirty }"
            :disabled="form.processing || verification.processing || !form.isDirty">
            {{ __(confirmationButtonText) }}
            <template #icon>
                <div v-html="confirmationButtonIcon" />
            </template>
        </PrimaryButton>
    </div>

    <UnsavedChangesConfirmationModal v-if="cancelling" :form="form" @close="cancelling = false"
        :handler="goToIndexPage" />
    <DeleteConfirmationModal v-if="confirmingDeletion" :form="form" @close="confirmingDeletion = false" />
</template>