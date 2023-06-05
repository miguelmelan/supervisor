<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    form: Object,
    selection: Object,
    add: Function,
    remove: Function,
    reset: Function,
    alreadySelected: Function,
});
</script>

<template>
    <div v-if="selection.orchestrator_connection" class="col-span-6">
        <PrimaryButton as="a" @click.prevent="add" :class="{ 'opacity-25': selection.tenants.length == 0 || form.processing }"
            :disabled="selection.tenants.length == 0 || form.processing">
            <span v-if="alreadySelected(selection.orchestrator_connection.id)">
                {{ __('Save') }}
            </span>
            <span v-else>
                {{ __('Add') }}
            </span>
            <template #icon>
                <svg v-if="alreadySelected(selection.orchestrator_connection.id)" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </template>
        </PrimaryButton>
        <DangerButton v-if="alreadySelected(selection.orchestrator_connection.id)" as="a"
            @click.prevent="remove(selection.orchestrator_connection.id); reset();" class="ml-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing">
            {{ __('Remove') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </template>
        </DangerButton>
        <SecondaryButton v-if="alreadySelected(selection.orchestrator_connection.id)" as="a" @click.prevent="reset()"
            class="ml-3">
            {{ __('Close') }}
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>
        </SecondaryButton>
    </div>
</template>