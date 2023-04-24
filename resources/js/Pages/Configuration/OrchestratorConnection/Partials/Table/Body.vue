<script setup>
import Actions from './Actions.vue';
import VerificationStatus from '@/Components/VerificationStatus.vue';

const props = defineProps({
    orchestratorConnections: Object,
    selected: Array,
    edit: Function,
    remove: Function,
});
</script>

<template>
    <tbody>
        <tr v-for="item in orchestratorConnections.data" :key="item.id" class="bg-white border-b">
            <td class="p-4 w-4">
                <div class="flex items-center">
                    <input type="checkbox"
                        class="rounded border-gray-400 hover:border-gray-neutral-55 text-blue-50 shadow-sm focus:border-blue-50"
                        v-bind="selected" :name="'checkbox-' + item.id" :value="item.id" number>
                    <label :for="'checkbox-' + item.id" class="sr-only">{{ __('Select item') }}</label>
                </div>
            </td>
            <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                {{ item.code }}
            </th>
            <td class="py-4 px-6">
                {{ item.name }}
            </td>
            <td class="py-4 px-6">
                {{ __(item.hosting_type === 'cloud' ? 'Cloud' : 'On-Premise') }}
            </td>
            <td class="py-4 px-6">
                {{ __(
                item.environment_type === 'development' ? 'Development' :
                item.environment_type === 'test' ? 'Test' :
                item.environment_type === 'production' ? 'Production' :
                item.environment_type === 'hybrid' ? 'Hybrid' : 'Unknown'
                ) }}
            </td>
            <td class="py-4 px-6">
                <VerificationStatus :verified="item.verified" :verified-at-for-humans="item.verified_at_for_humans"
                    :light="true" />
            </td>
            <td class="py-4 px-6">
                <Actions :item="item" :edit="edit" :remove="remove" />
            </td>
        </tr>
    </tbody>
</template>