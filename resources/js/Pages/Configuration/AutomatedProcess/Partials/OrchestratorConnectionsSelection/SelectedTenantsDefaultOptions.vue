<script setup>
import Toggle from '@/Components/Toggle.vue';
import { onBeforeUpdate } from 'vue';

const props = defineProps({
    selection: Object,
});

onBeforeUpdate(() => {
    if (props.selection.orchestrator_connection && !props.selection.orchestrator_connection.pivot) {
        props.selection.orchestrator_connection.pivot = {
            built_in_alerts: true,
            processes_supervision: true,
            machines_supervision: true,
            queues_supervision: true,
            kibana_built_in_alerts: false,
        };
    }
});
</script>

<template>
    <div v-if="selection.orchestrator_connection" class="col-span-6">
        <h3 class="text-md font-medium text-gray-900">
            {{ __('Selected tenants default options') }}
        </h3>
    </div>
    <!-- <div v-if="selection.orchestrator_connection" class="col-span-6">
        <Toggle id="orchestrator-connection-built-in-alerts" v-model:checked="selection.orchestrator_connection.pivot.built_in_alerts"
            :label="__('Enable UiPath Orchestrator built-in alerts')" />
    </div> -->
    <div v-if="selection.orchestrator_connection" class="col-span-6">
        <Toggle id="orchestrator-connection-processes-supervision"
            v-model:checked="selection.orchestrator_connection.pivot.processes_supervision"
            :label="__('Enable UiPath processes supervision')" />
    </div>
    <div v-if="selection.orchestrator_connection" class="col-span-6">
        <Toggle id="orchestrator-connection-machine-session-runtimes-supervision" v-model:checked="selection.orchestrator_connection.pivot.machines_supervision"
            :label="__('Enable UiPath machines supervision')" />
    </div>
    <div v-if="selection.orchestrator_connection" class="col-span-6">
        <Toggle id="orchestrator-connection-queues-supervision" v-model:checked="selection.orchestrator_connection.pivot.queues_supervision"
            :label="__('Enable UiPath queues supervision')" />
    </div>
    <div v-if="selection.orchestrator_connection && selection.orchestrator_connection.kibana_enabled"
        class="col-span-6">
        <Toggle id="orchestrator-connection-kibana-built-in-alerts"
            v-model:checked="selection.orchestrator_connection.pivot.kibana_built_in_alerts" :label="__('Enable Kibana built-in alerts')" />
    </div>
</template>