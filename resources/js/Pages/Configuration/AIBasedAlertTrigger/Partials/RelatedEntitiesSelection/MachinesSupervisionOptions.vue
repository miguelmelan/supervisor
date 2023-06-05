<script setup>
import SectionBorder from '@/Components/SectionBorder.vue';
import Toast from '@/Components/Toast.vue';

const props = defineProps({
    selectedTenant: Object,
    addMachineToSelectedTenant: Function,
    removeMachineFromSelectedTenant: Function,
});
</script>

<template>
    <div class="col-span-6">
        <SectionBorder />
        <h3 class="text-md font-medium text-gray-900">
            {{ __('UiPath machines') }}
            <span
                v-if="selectedTenant.hasOwnProperty('selected_machines') && selectedTenant.selected_machines.length > 0"
                class="inline-flex justify-center items-center p-3 ml-1 w-3 h-3 text-sm font-medium text-blue-600 bg-blue-200 rounded-full">
                {{ selectedTenant.selected_machines.length.toString() }}
            </span>
        </h3>
    </div>
    <div v-if="selectedTenant.treeviews && Object.keys(selectedTenant.treeviews.machines.nodes).length > 0"
        class="col-span-6 text-xs">
        <Treeview :config="selectedTenant.treeviews.machines.config" :nodes="selectedTenant.treeviews.machines.nodes"
            @nodeChecked="addMachineToSelectedTenant" @nodeUnchecked="removeMachineFromSelectedTenant" />
    </div>
    <div v-else class="col-span-6">
        <Toast :id="`tenant-machines-not-found-${selectedTenant.id}`" level="warning"
            :message="__('Unable to find machines on this UiPath Orchestrator tenant.')" :closable="false" />
    </div>
</template>