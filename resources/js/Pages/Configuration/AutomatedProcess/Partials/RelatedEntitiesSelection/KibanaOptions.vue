<script setup>
import SectionBorder from '@/Components/SectionBorder.vue';
import Toggle from '@/Components/Toggle.vue';

const props = defineProps({
    selectedOrchestratorConnection: Object,
    selectedTenant: Object,
});
</script>

<template>
    <div v-if="selectedOrchestratorConnection.kibana_enabled" class="col-span-6">
        <SectionBorder />
        <Toggle id="orchestrator-connection-kibana-built-in-alerts"
            v-model:checked="selectedTenant.pivot.kibana_built_in_alerts"
            :label="__('Enable Kibana built-in alerts')" />
    </div>
</template>