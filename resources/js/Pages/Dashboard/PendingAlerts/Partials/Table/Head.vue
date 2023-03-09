<script setup>
const props = defineProps({
    alerts: Object,
    selectAll: Boolean,
    sorting: Object,
    sort: Function,
});
</script>

<template>
    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
        <tr>
            <th scope="col" class="p-4">
                <div class="flex items-center">
                    <input type="checkbox" class="rounded text-blue-50 shadow-sm focus:border-blue-50" :class="{
                        'bg-gray-100 border-gray-300 hover:border-gray-400': alerts.data.length == 0,
                        'bg-white border-gray-400 hover:border-gray-neutral-55': alerts.data.length > 0
                    }" v-model="selectAll" name="checkbox-all"
                        :disabled="alerts.data.length == 0">
                    <label for="checkbox-all" class="sr-only">{{ __('Select all items')}}</label>
                </div>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="" class="flex items-center">
                    <span>{{ __('Creation time') }}</span>
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="" class="flex items-center">
                    <span>{{ __('Severity') }}</span>
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="" class="flex items-center">
                    <span>{{ __('Type') }}</span>
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="" class="flex items-center">
                    <span>{{ __('Component') }}</span>
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="" class="flex items-center">
                    <span>{{ __('Orchestrator connection') }}</span>
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="" class="flex items-center">
                    <span>{{ __('Automated business process') }}</span>
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                {{ __('Actions') }}
            </th>
        </tr>
    </thead>
</template>