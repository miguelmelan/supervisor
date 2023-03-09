<script setup>
import Actions from './Actions.vue';
import VerificationStatus from '@/Components/VerificationStatus.vue';

const props = defineProps({
    alerts: Object,
    selected: Array,
    edit: Function,
    remove: Function,
});
</script>

<template>
    <tbody>
        <tr v-for="item in alerts.data" :key="item.id" class="bg-white border-b">
            <td class="p-4 w-4">
                <div class="flex items-center">
                    <input type="checkbox"
                        class="rounded border-gray-400 hover:border-gray-neutral-55 text-blue-50 shadow-sm focus:border-blue-50"
                        v-model="selected" :name="'checkbox-' + item.id" :value="item.id" number>
                    <label :for="'checkbox-' + item.id" class="sr-only">{{ __('Select item') }}</label>
                </div>
            </td>
            <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                {{ item.id }}
            </th>
            <td class="py-4 px-6">
                {{ item.creation_time }}
            </td>
            <td class="py-4 px-6">
                {{ __(item.severity) }}
            </td>
            <td class="py-4 px-6">
                {{ __(item.notification_name) }}
            </td>
            <td class="py-4 px-6">
                {{ __(item.component) }}
            </td>
            <td class="py-4 px-6">
                {{ __(item.tenant.orchestrator_connection.id) }}
            </td>
            <td class="py-4 px-6">
                {{ __(item.automated_process.id) }}
            </td>
            <td class="py-4 px-6">
                <Actions :item="item" :edit="edit" :remove="remove" />
            </td>
        </tr>
    </tbody>
</template>