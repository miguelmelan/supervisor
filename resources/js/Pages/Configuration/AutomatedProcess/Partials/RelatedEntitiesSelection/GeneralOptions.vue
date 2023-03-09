<script setup>
import Toggle from '@/Components/Toggle.vue';

const props = defineProps({
    selectedTenant: Object,
});
</script>

<template>
    <div class="col-span-6">
        <h3 class="text-md font-medium text-gray-900">
            {{ __('General options') }}
        </h3>
    </div>
    <div class="col-span-6 sm:col-span-4">
        <Toggle id="orchestrator-connection-built-in-alerts" v-model:checked="selectedTenant.pivot.built_in_alerts"
            :label="__('Enable UiPath Orchestrator built-in alerts')" />
    </div>
</template>