<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    selected: Array
});

const emit = defineEmits(['close']);

const confirmingDeletion = ref(true);
const deleting = ref(false);

const bulkDelete = () => {
    deleting.value = true;
    Inertia.post(route('configuration.ai-based-alert-triggers.bulk-destroy'), {
        selected: props.selected
    }, {
        onSuccess: () => {
            close();
            deleting.value = false;
        }
    });
};

const close = () => {
    confirmingDeletion.value = false;
    emit('close');
};
</script>

<template>
    <ConfirmationModal :show="confirmingDeletion" @close="close" :closeable="!deleting" severity="error">
        <template #title>
            {{ __('Delete multiple AI based alert triggers') }}
        </template>

        <template #content>
            {{
                __('Are you sure you want to delete selected AI based alert triggers (:number)? Once removed, all of their resources and data will be permanently deleted.', {
                    number: selected.length.toString()
                }) 
            }}
        </template>

        <template #footer>
            <SecondaryButton @click="close" :class="{ 'opacity-25': deleting }"
                :disabled="deleting">
                {{ __('Cancel') }}
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
            </SecondaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': deleting }" :disabled="deleting"
                @click="bulkDelete">
                {{ __('Delete') }}
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </template>
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>