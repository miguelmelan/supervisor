<script setup>
import FormStep from '@/Components/FormStep.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    form: Object,
    mode: String,
    alertTrigger: {
        type: Object,
        default: null,
    },
    automatedProcesses: Array,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});

const emit = defineEmits(['updated']);

const selected = ref(props.alertTrigger ? props.alertTrigger.automatedProcesses.map(ap => ap.id) : []);

const selectAll = computed({
    get() {
        return props.automatedProcesses.length > 0 ? selected.value.length == props.automatedProcesses.length : false;
    },
    set(value) {
        let values = [];
        if (value) {
            props.automatedProcesses.forEach(function (automatedProcess) {
                values.push(automatedProcess.id);
            });
        }
        
        selected.value = values;
    }
});

watch(selected, () => {
    emit('updated', selected);
});
</script>

<template>
    <SectionBorder v-if="withTopBorder" />
    <FormStep>
        <template #title>
            {{ __('Automated business processes selection') }}
        </template>

        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('This section enables the user to establish a connection between the trigger and specific Automated business processes that should be associated with it. Within this section, users can select one or multiple Automated business processes from a list of available options.') }}
                </p>
            </div>
        </template>

        <template #form>
            <div class="col-span-6">
                <table v-if="automatedProcesses.length > 0" class="min-w-full divide-y divide-y-gray-200 text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input type="checkbox"
                                        class="rounded text-blue-50 shadow-sm focus:border-blue-50"
                                        :class="{
                                            'bg-gray-100 border-gray-300 hover:border-gray-400': automatedProcesses.length == 0,
                                            'bg-white border-gray-400 hover:border-gray-neutral-55': automatedProcesses.length > 0
                                        }"
                                        v-model="selectAll" name="checkbox-all"
                                        :disabled="automatedProcesses.length == 0">
                                    <label for="checkbox-all" class="sr-only">{{ __('Select all items')}}</label>
                                </div>
                            </th>
                            <th scope="col" class="py-3 px-6">{{ __('Code') }}</th>
                            <th scope="col" class="py-3 px-6">{{ __('Name') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in automatedProcesses" :key="item.id"
                            class="bg-white border-b">
                            <td class="p-4 w-4">
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-400 hover:border-gray-neutral-55 text-blue-50 shadow-sm focus:border-blue-50"
                                        v-model="selected" :name="'checkbox-' + item.id" :value="item.id" number>
                                    <label :for="'checkbox-' + item.id" class="sr-only">{{ __('Select item') }}</label>
                                </div>
                            </td>
                            <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                {{ item.code }}
                            </th>
                            <td class="py-4 px-6">
                                {{ item.name }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </FormStep>
</template>