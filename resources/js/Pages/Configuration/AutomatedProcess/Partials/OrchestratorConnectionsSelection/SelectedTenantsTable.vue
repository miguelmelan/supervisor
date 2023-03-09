<script setup>
const props = defineProps({
    selection: Object,
    selectAll: Boolean,
});
</script>

<template>
    <div v-if="selection.orchestrator_connection" class="mt-4 max-h-80 overflow-auto relative">
        <table class="w-full text-left text-gray-500">
            <caption class="py-5 text-left text-md font-medium text-gray-900 bg-white">
                {{ __('Tenants') }}
                <p class="mt-1 text-sm font-normal text-gray-500">
                    {{ __('Select one or multiple tenants') }}
                </p>
            </caption>
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="p-4 w-1/5">
                        <div class="flex items-center">
                            <input type="checkbox"
                                class="rounded text-blue-50 shadow-sm focus:border-blue-50" :class="{
                                    'bg-gray-100 border-gray-300 hover:border-gray-400': selection.orchestrator_connection.tenants.length == 0,
                                    'bg-white border-gray-400 hover:border-gray-neutral-55': selection.orchestrator_connection.tenants.length > 0
                                }" v-model="selectAll" name="checkbox-all"
                                :disabled="selection.orchestrator_connection.tenants.length == 0">
                            <label for="checkbox-all" class="sr-only">{{ __('Select all items')}}</label>
                        </div>
                    </th>
                    <th scope="col" class="py-3 px-6 w-4/5">
                        {{ __('Name') }}
                    </th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <tr v-for="tenant in selection.orchestrator_connection.tenants" :key="tenant.id"
                    class="bg-white border-b">
                    <td scope="row" class="p-4">
                        <div class="flex items-center">
                            <input type="checkbox"
                                class="rounded border-gray-400 hover:border-gray-neutral-55 text-blue-50 shadow-sm focus:border-blue-50"
                                v-model="selection.tenants" :name="'checkbox-' + tenant.id"
                                :value="tenant.id" number>
                            <label :for="'checkbox-' + tenant.id" class="sr-only">{{ __('Select item')
                            }}</label>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        {{ tenant.name }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>