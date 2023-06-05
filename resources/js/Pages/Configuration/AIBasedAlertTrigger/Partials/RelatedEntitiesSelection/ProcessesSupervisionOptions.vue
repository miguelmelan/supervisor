<script setup>
import Toast from '@/Components/Toast.vue';

const props = defineProps({
    selectedTenant: Object,
    addReleaseToSelectedTenant: Function,
    removeReleaseFromSelectedTenant: Function,
});
</script>

<template>
    <div class="col-span-6">
        <h3 class="text-md font-medium text-gray-900">
            {{ __('UiPath processes') }}
            <span
                v-if="selectedTenant.hasOwnProperty('selected_releases') && selectedTenant.selected_releases.length > 0"
                class="inline-flex justify-center items-center p-3 ml-1 w-3 h-3 text-sm font-medium text-blue-600 bg-blue-200 rounded-full">
                {{ selectedTenant.selected_releases.length.toString() }}
            </span>
        </h3>
    </div>
    <div v-if="selectedTenant.treeviews && Object.keys(selectedTenant.treeviews.releases.nodes).length > 0"
        class="col-span-6 text-xs">
        <Treeview :config="selectedTenant.treeviews.releases.config" :nodes="selectedTenant.treeviews.releases.nodes"
            @nodeChecked="addReleaseToSelectedTenant" @nodeUnchecked="removeReleaseFromSelectedTenant" />
    </div>
    <div v-else class="col-span-6">
        <Toast :id="`tenant-processes-not-found-${selectedTenant.id}`" level="warning"
            :message="__('Unable to find processes on this UiPath Orchestrator tenant.')" :closable="false" />
    </div>
</template>