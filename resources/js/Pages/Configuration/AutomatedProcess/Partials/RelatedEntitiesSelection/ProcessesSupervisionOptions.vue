<script setup>
import SectionBorder from '@/Components/SectionBorder.vue';
import Toast from '@/Components/Toast.vue';
import Toggle from '@/Components/Toggle.vue';

const props = defineProps({
    selectedTenant: Object,
    addReleaseToSelectedTenant: Function,
    removeReleaseFromSelectedTenant: Function,
});
</script>

<template>
    <div class="col-span-6">
        <!-- <SectionBorder /> -->
        <h3 class="text-md font-medium text-gray-900">
            {{ __('UiPath processes supervision') }}
            <span
                v-if="selectedTenant.hasOwnProperty('selected_releases') && selectedTenant.selected_releases.length > 0"
                class="inline-flex justify-center items-center p-3 ml-1 w-3 h-3 text-sm font-medium text-blue-600 bg-blue-200 rounded-full">
                {{ selectedTenant.selected_releases.length.toString() }}
            </span>
        </h3>
    </div>
    <div class="col-span-6 sm:col-span-4">
        <Toggle id="orchestrator-connection-processes-supervision"
            v-model:checked="selectedTenant.pivot.processes_supervision" :label="__('Enable')"
            :_disabled="!selectedTenant.treeviews" />
    </div>
    <div v-if="selectedTenant.treeviews && Object.keys(selectedTenant.treeviews.releases.nodes).length > 0 && selectedTenant.pivot.processes_supervision"
        class="col-span-6 text-xs">
        <Treeview :config="selectedTenant.treeviews.releases.config" :nodes="selectedTenant.treeviews.releases.nodes"
            @nodeChecked="addReleaseToSelectedTenant" @nodeUnchecked="removeReleaseFromSelectedTenant" />
    </div>
    <div v-else-if="selectedTenant.pivot.processes_supervision" class="col-span-6">
        <Toast :id="`tenant-processes-not-found-${selectedTenant.id}`" level="warning"
            :message="__('Unable to find processes on this UiPath Orchestrator tenant.')" :closable="false" />
    </div>
</template>