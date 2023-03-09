<script setup>
import 'flowbite';
import AlertCreationDateFilter from './Alert/CreationDate.vue';
import AlertSimpleFieldFilter from './Alert/SimpleField.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { onMounted, ref, computed, reactive } from 'vue';

const props = defineProps({
    data: {
        type: Object,
        default: {
            alert: {
                creationDateRange: null,
                selectedSeverities: null,
                selectedNotificationNames: null,
                selectedComponents: null,
            },
        },
    },
    alertsProperties: Object,
});

const emit = defineEmits([
    'alert-property-updated',
]);

const filtersCount = computed(() => alertFiltersCount)

const alertFiltersCount = computed(() => {
    let count = 0;
    if (alertCreationDateRange && alertCreationDateRange.length === 2) {
        count++;
    }
    if (selectedAlertSeverities.value && selectedAlertSeverities.value.length > 0) {
        count++;
    }
    if (selectedAlertNotificationNames.value && selectedAlertNotificationNames.value.length > 0) {
        count++;
    }
    if (selectedAlertComponents.value && selectedAlertComponents.value.length > 0) {
        count++;
    }

    return count;
});

const alertCreationDateRange = reactive(props.data.alert.creationDateRange);
const updateAlertCreationDateRange = (data) => {
    Object.assign(alertCreationDateRange, data);
    emit('alert-property-updated');
};

const selectedAlertSeverities = computed(() => props.data.alert.selectedSeverities);

const selectedAlertNotificationNames = computed(() => props.data.alert.selectedNotificationNames);
const toggleAlertNotificationName = (notificationName) => {
    if (selectedAlertNotificationNames.value && selectedAlertNotificationNames.value.includes(notificationName)) {
        selectedAlertNotificationNames.value.splice(selectedAlertNotificationNames.value.indexOf(notificationName), 1);
    } else {
        selectedAlertNotificationNames.value.push(notificationName);
    }
    emit('alert-property-updated');
};

const selectedAlertComponents = computed(() => props.data.alert.selectedComponents);
const toggleAlertComponent = (component) => {
    if (selectedAlertComponents.value && selectedAlertComponents.value.includes(component)) {
        selectedAlertComponents.value.splice(selectedAlertComponents.value.indexOf(component), 1);
    } else {
        selectedAlertComponents.value.push(component);
    }
    emit('alert-property-updated');
};

onMounted(() => {    
    const accordionConfiguration = {
        items: [
            {
                id: 'alert-properties-heading',
                triggerEl: document.querySelector('#alert-properties-heading'),
                targetEl: document.querySelector('#alert-properties-body'),
                active: alertFiltersCount.value > 0,
            },
            {
                id: 'orchestrator-connection-properties-heading',
                triggerEl: document.querySelector('#orchestrator-connection-properties-heading'),
                targetEl: document.querySelector('#orchestrator-connection-properties-body'),
                active: false,
            },
        ],
        options: {
            alwaysOpen: true,
            activeClasses: 'bg-gray-100 text-gray-900',
            inactiveClasses: 'bg-gray-50 text-gray-500',
            onOpen: (item) => {
                console.log('accordion item has been shown');
                //console.log(item);
            },
            onClose: (item) => {
                console.log('accordion item has been hidden');
                //console.log(item);
            },
            onToggle: (item) => {
                console.log('accordion item has been toggled');
                //console.log(item);
            },
        },
    };

    new Accordion(accordionConfiguration.items, accordionConfiguration.options); 
});
</script>

<template>
    <div class="p-4 bg-white rounded-md">
        <div class="flex items-center text-gray-600 ">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
            </svg>
            <div class="ml-4 text-md leading-7 font-semibold">
                {{ __('Filters') }}
            </div>
            <span class="bg-gray-700 text-gray-300 text-sm font-medium ml-2 px-2.5 py-0.5 rounded-full">{{ filtersCount }}</span>
        </div>
    
        <div class="mt-4">
            <h2 id="alert-properties-heading">
                <button type="button"
                    class="flex items-center justify-between w-full p-2 font-medium text-left text-sm text-gray-500 border border-b-0 border-gray-200 rounded-t-sm hover:bg-gray-100"
                    aria-expanded="false" aria-controls="alert-properties-body">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" />
                        </svg>
                        <span>{{ __('Alert') }}</span>
                    </div>
                    <span class="bg-gray-700 text-gray-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ alertFiltersCount }}
                    </span>
                </button>
            </h2>
            <div id="alert-properties-body" class="hidden" aria-labelledby="alert-properties-heading">
                <div class="p-2 border border-b-0 border-gray-200">
                    <div class="grid grid-cols-4 gap-4">
                        <AlertCreationDateFilter :range="alertCreationDateRange" @updated="updateAlertCreationDateRange" />
                        <AlertSimpleFieldFilter :values="alertsProperties.severity"
                            :selected-values="selectedAlertSeverities"
                            :label="__('Severity')" :placeholder="__('Select severity levels')"
                            @updated="emit('alert-property-updated')" />
                        <AlertSimpleFieldFilter :values="alertsProperties.notificationName"
                            :selected-values="selectedAlertNotificationNames"
                            :label="__('Type')" :placeholder="__('Select types')"
                            @updated="emit('alert-property-updated')" />
                        <AlertSimpleFieldFilter :values="alertsProperties.component"
                            :selected-values="selectedAlertComponents"
                            :label="__('Component')" :placeholder="__('Select components')"
                            @updated="emit('alert-property-updated')" />
                    </div>
                </div>
            </div>
            <h2 id="orchestrator-connection-properties-heading">
                <button type="button"
                    class="flex items-center justify-between w-full p-2 font-medium text-left text-sm text-gray-500 border border-gray-200 hover:bg-gray-100"
                    aria-expanded="false" aria-controls="orchestrator-connection-properties-body">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                        <span>{{ __('UiPath Orchestrator') }}</span>
                    </div>
                    <span class="bg-gray-700 text-gray-300 text-xs font-medium px-2.5 py-0.5 rounded-full">0</span>
                </button>
            </h2>
            <div id="orchestrator-connection-properties-body" class="hidden" aria-labelledby="orchestrator-connection-properties-heading">
                <div class="p-2 border border-t-0 border-gray-200">
                    <div class="grid grid-cols-4 gap-4">
                        <div class="p-4">
                            <InputLabel :value="__('UiPath Orchestrator / Tenant')" />
                            <Dropdown align="right" width="full" :contentClasses="['py-0', 'bg-gray-50', 'border', 'border-gray-300']" class="mt-1" :stay-opened="true">
                                <template #trigger>
                                    <button type="button"
                                        class="inline-flex text-left items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md w-full p-2.5 focus:outline-none transition">
                                        {{ __('Select UiPath Orchestrator / Tenant')}}
                                    </button>
                                </template>
                                <template #content>
                                    <div class="w-full">
                                        <DropdownLink as="a" :withBottomBorder="true" :with-checkbox="true">
                                            {{ __('Fatal') }}
                                        </DropdownLink>
                                        <DropdownLink as="a" :withBottomBorder="true" :with-checkbox="true">
                                            {{ __('Error') }}
                                        </DropdownLink>
                                        <DropdownLink as="a" :withBottomBorder="true" :with-checkbox="true">
                                            {{ __('Warning') }}
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        <div class="p-4">
                            <InputLabel :value="__('Hosting Type')" />
                            <Dropdown align="right" width="full" :contentClasses="['py-0', 'bg-gray-50', 'border', 'border-gray-300']" class="mt-1">
                                <template #trigger>
                                    <button type="button"
                                        class="inline-flex text-left items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md w-full p-2.5 focus:outline-none transition">
                                        {{ __('Select hosting types')}}
                                    </button>
                                </template>
                                <template #content>
                                    <div class="w-full">
                                        <DropdownLink as="a" @click.prevent="true" href="#" :withBottomBorder="true">
                                            {{ __('Fatal') }}
                                        </DropdownLink>
                                        <DropdownLink as="a" @click.prevent="true" href="#" :withBottomBorder="true">
                                            {{ __('Error') }}
                                        </DropdownLink>
                                        <DropdownLink as="a" @click.prevent="true" href="#" :withBottomBorder="true">
                                            {{ __('Warning') }}
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                        <div class="p-4">
                            <InputLabel :value="__('Environment Type')" />
                            <Dropdown align="right" width="full" :contentClasses="['py-0', 'bg-gray-50', 'border', 'border-gray-300']" class="mt-1">
                                <template #trigger>
                                    <button type="button"
                                        class="inline-flex text-left items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md w-full p-2.5 focus:outline-none transition">
                                        {{ __('Select environment types')}}
                                    </button>
                                </template>
                                <template #content>
                                    <div class="w-full">
                                        <DropdownLink as="a" @click.prevent="true" href="#" :withBottomBorder="true">
                                            {{ __('Fatal') }}
                                        </DropdownLink>
                                        <DropdownLink as="a" @click.prevent="true" href="#" :withBottomBorder="true">
                                            {{ __('Error') }}
                                        </DropdownLink>
                                        <DropdownLink as="a" @click.prevent="true" href="#" :withBottomBorder="true">
                                            {{ __('Warning') }}
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>