<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    selected: Array,
    action: String,
    do: Function,
});

const emit = defineEmits(['close']);

const confirming = ref(true);
const doing = ref(false);

const bulkAction = () => {
    doing.value = true;
    props.do(close);
};

const close = () => {
    doing.value = false;
    confirming.value = false;
    emit('close');
};
</script>

<template>
    <ConfirmationModal :show="confirming" @close="close" :closeable="!doing" severity="info">
        <template #title>
            {{ __(`${action.charAt(0).toUpperCase() + action.slice(1)} multiple pending alerts`) }}
        </template>

        <template #content>
            {{
                __(`Are you sure you want to ${action} selected pending alerts (:number)?`, {
                    number: selected.length.toString()
                }) 
            }}
        </template>

        <template #footer>
            <SecondaryButton @click="close" :class="{ 'opacity-25': doing }"
                :disabled="doing">
                {{ __('Cancel') }}
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': doing }" :disabled="doing"
                @click="bulkAction">
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