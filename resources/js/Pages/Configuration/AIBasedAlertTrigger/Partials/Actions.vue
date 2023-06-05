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
    withTopBorder: {
        type: Boolean,
        default: true
    },
    relatedEntitiesSelected: Boolean,
});

const confirmationButtonText = computed(() => {
    let text = '';
    if (props.mode == 'create') {
        text = 'Define an AI based alert trigger';
    } else if (props.mode == 'edit') {
        text = 'Save';
    }
    return text;
});

const confirmationButtonIcon = computed(() => {
    let icon = '';
    if (props.mode == 'create') {
        icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />';
    } else if (props.mode == 'edit') {
        icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
    }
    return icon;
});

const confirmingDeletion = ref(false);

const goToIndexPage = () => {
    Inertia.get(route('configuration.ai-based-alert-triggers.index'));
};
</script>

<template>
    <SectionBorder v-if="withTopBorder" />
    <ActionMessage v-if="mode == 'edit'" :on="form.recentlySuccessful" class="text-right" :class="{ 'mb-4': form.recentlySuccessful }">
        {{ __('Saved.') }}
    </ActionMessage>
    <ActionMessage :on="form.hasErrors" class="text-right" :class="{ 'mb-4': form.hasErrors }">
        {{ __('There are errors on the form. Please fix them before continuing.') }}
    </ActionMessage>
    <div class="flex items-center justify-end">
        <SecondaryButton as="button" @click="form.isDirty ? cancelling = true : goToIndexPage()" class="mr-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing">
            {{ __('Close') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>
        </SecondaryButton>
        <DangerButton v-if="mode == 'edit'" as="button" @click="confirmingDeletion = true" class="mr-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing">
            {{ __('Delete') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </template>
        </DangerButton>
        <PrimaryButton :class="{ 'opacity-25': form.processing || !form.isDirty || !relatedEntitiesSelected || form.conditions.trim() === '' || form.recurrence.trim() === '' || form.crons.length === 0 }"
            :disabled="form.processing || !form.isDirty || !relatedEntitiesSelected || form.conditions.trim() === '' || form.recurrence.trim() === '' || form.crons.length === 0">
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