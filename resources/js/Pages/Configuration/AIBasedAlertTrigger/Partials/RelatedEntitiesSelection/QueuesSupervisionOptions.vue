<script setup>
import SectionBorder from '@/Components/SectionBorder.vue';
import Toast from '@/Components/Toast.vue';

const props = defineProps({
    selectedTenant: Object,
    addQueueToSelectedTenant: Function,
    removeQueueFromSelectedTenant: Function,
});
</script>

<template>
    <div class="col-span-6">
        <SectionBorder />
        <h3 class="text-md font-medium text-gray-900">
            {{ __('UiPath queues') }}
            <span v-if="selectedTenant.hasOwnProperty('selected_queues') && selectedTenant.selected_queues.length > 0"
                class="inline-flex justify-center items-center p-3 ml-1 w-3 h-3 text-sm font-medium text-blue-600 bg-blue-200 rounded-full">
                {{ selectedTenant.selected_queues.length.toString() }}
            </span>
        </h3>
    </div>
    <div v-if="selectedTenant.treeviews && Object.keys(selectedTenant.treeviews.queueDefinitions.nodes).length > 0"
        class="col-span-6 sm:col-span-4 text-xs">
        <Treeview :config="selectedTenant.treeviews.queueDefinitions.config"
            :nodes="selectedTenant.treeviews.queueDefinitions.nodes" @nodeChecked="addQueueToSelectedTenant"
            @nodeUnchecked="removeQueueFromSelectedTenant" />
    </div>
    <div v-else class="col-span-6">
        <Toast :id="`tenant-queues-not-found-${selectedTenant.id}`" level="warning"
            :message="__('Unable to find queues on this UiPath Orchestrator tenant.')" :closable="false" />
    </div>
</template>