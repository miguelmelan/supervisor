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
});

const confirmationButtonText = computed(() => {
    let text = '';
    if (props.mode == 'create') {
        text = 'Define a business automated process';
    } else if (props.mode == 'edit') {
        text = 'Save';
    }
    return text;
});

const confirmationButtonIcon = computed(() => {
    let icon = '';
    if (props.mode == 'create') {
        icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>';
    } else if (props.mode == 'edit') {
        icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
    }
    return icon;
});

const confirmingDeletion = ref(false);

const goToIndexPage = () => {
    Inertia.get(route('configuration.automated-processes.index'));
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
        <PrimaryButton :class="{ 'opacity-25': form.processing || !form.isDirty }"
            :disabled="form.processing || !form.isDirty">
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