<script setup>
import 'flowbite';
import AlertCreationDateFilter from './Alert/CreationDate.vue';
import AlertLockingDateFilter from './Alert/LockingDate.vue';
import AlertLockingUserFilter from './Alert/LockingUser.vue';
import SimpleFieldFilter from './SimpleField.vue';
import OrchestratorConnectionFilter from './OrchestratorConnection/Self.vue';
import OrchestratorConnectionTenantFilter from './OrchestratorConnection/Tenant.vue';
import { onMounted, inject, computed } from 'vue';

const props = defineProps({
    data: {
        type: Object,
        default: {
            alert: {
                creationDateRange: null,
                lockingDateRange: null,
                selectedLockingUsers: null,
                selectedSeverities: null,
                selectedNotificationNames: null,
                selectedComponents: null,
            },
            orchestratorConnection: {
                selected: null,
                selectedTenants: null,
                selectedHostingTypes: null,
                selectedEnvironmentTypes: null,
            },
        },
    },
    alertsProperties: Object,
    orchestratorConnectionsProperties: Object,
});

const translate = inject('translate');

const emit = defineEmits([
    'property-updated',
]);

const filtersCount = computed(() => alertFiltersCount.value + orchestratorConnectionFiltersCount.value)

const alertFiltersCount = computed(() => {
    let count = 0;
    if (alertCreationDateRange.value && alertCreationDateRange.value.length === 2) {
        count++;
    }
    if (alertLockingDateRange.value && alertLockingDateRange.value.length === 2) {
        count++;
    }
    if (selectedLockingUsers.value && selectedLockingUsers.value.length > 0) {
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

const orchestratorConnectionFiltersCount = computed(() => {
    let count = 0;
    if (selectedOrchestratorConnections.value && selectedOrchestratorConnections.value.length > 0) {
        count ++;
    }
    if (selectedOrchestratorConnectionTenants.value && selectedOrchestratorConnectionTenants.value.length > 0) {
        count ++;
    }
    if (selectedOrchestratorConnectionHostingTypes.value && selectedOrchestratorConnectionHostingTypes.value.length > 0) {
        count++;
    }
    if (selectedOrchestratorConnectionEnvironmentTypes.value && selectedOrchestratorConnectionEnvironmentTypes.value.length > 0) {
        count++;
    }
    return count;
});

const sendPropertyUpdatedEvent = () => {
    emit('property-updated', filtersCount.value);
};

const alertCreationDateRange = computed(() => props.data.alert.creationDateRange);
const updateAlertCreationDateRange = (data) => {
    alertCreationDateRange.value.splice(0, alertCreationDateRange.value.length);
    if (data) {
        alertCreationDateRange.value.push(...data);
    }
    sendPropertyUpdatedEvent();
};
const alertLockingDateRange = computed(() => props.data.alert.lockingDateRange);
const updateAlertLockingDateRange = (data) => {
    alertLockingDateRange.value.splice(0, alertLockingDateRange.value.length);
    if (data) {
        alertLockingDateRange.value.push(...data);
    }
    sendPropertyUpdatedEvent();
};
const selectedLockingUsers = computed(() => props.data.alert.selectedLockingUsers);
const selectedAlertSeverities = computed(() => props.data.alert.selectedSeverities);
const selectedAlertNotificationNames = computed(() => props.data.alert.selectedNotificationNames);
const selectedAlertComponents = computed(() => props.data.alert.selectedComponents);
const resetAlertFilters = () => {
    alertCreationDateRange.value.splice(0, alertCreationDateRange.value.length);
    props.data.alert.selectedSeverities = [];
    props.data.alert.selectedNotificationNames = [];
    props.data.alert.selectedComponents = [];
    sendPropertyUpdatedEvent();
};

const selectedOrchestratorConnections = computed(() => props.data.orchestratorConnection.selected);
const selectedOrchestratorConnectionTenants = computed(() => props.data.orchestratorConnection.selectedTenants);
const selectedOrchestratorConnectionHostingTypes = computed(() => props.data.orchestratorConnection.selectedHostingTypes);
const selectedOrchestratorConnectionEnvironmentTypes = computed(() => props.data.orchestratorConnection.selectedEnvironmentTypes);
const resetOrchestratorConnectionFilters = () => {
    props.data.orchestratorConnection.selected = [];
    props.data.orchestratorConnection.selectedTenants = [];
    props.data.orchestratorConnection.selectedHostingTypes = [];
    props.data.orchestratorConnection.selectedEnvironmentTypes = [];
    sendPropertyUpdatedEvent();
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
                active: orchestratorConnectionFiltersCount.value > 0,
            },
        ],
        options: {
            alwaysOpen: true,
            activeClasses: 'bg-gray-100 text-gray-900',
            inactiveClasses: 'bg-gray-50 text-gray-500',
            onOpen: (item) => {
                /* console.log('accordion item has been shown');
                console.log(item); */
            },
            onClose: (item) => {
                /* console.log('accordion item has been hidden');
                console.log(item); */
            },
            onToggle: (item) => {
                /* console.log('accordion item has been toggled');
                console.log(item); */
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
                        <span>{{ __('Properties of alerts') }}</span>
                    </div>
                    <div class="flex">
                        <span class="bg-gray-700 text-gray-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ alertFiltersCount }}
                        </span>
                        <button v-if="alertFiltersCount > 0" @click.stop="resetAlertFilters()" type="button" class="inline-flex items-center p-0.5 ml-2 text-sm text-gray-400 bg-transparent rounded-sm hover:bg-gray-200 hover:text-gray-900" :aria-label="__('Remove')">
                            <svg aria-hidden="true" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">{{ __('Remove') }}</span>
                        </button>
                    </div>
                </button>
            </h2>
            <div id="alert-properties-body" class="hidden" aria-labelledby="alert-properties-heading">
                <div class="p-2 border border-b-0 border-gray-200">
                    <div class="grid grid-cols-4 gap-4">
                        <AlertCreationDateFilter :range="alertCreationDateRange" @updated="updateAlertCreationDateRange" />
                        <SimpleFieldFilter :values="alertsProperties.severity"
                            :selected-values="selectedAlertSeverities"
                            :label="__('Severity')" :placeholder="__('Select severity levels')"
                            @updated="sendPropertyUpdatedEvent" />
                        <SimpleFieldFilter :values="alertsProperties.notificationName"
                            :selected-values="selectedAlertNotificationNames"
                            :label="__('Type')" :placeholder="__('Select types')"
                            @updated="sendPropertyUpdatedEvent" />
                        <SimpleFieldFilter :values="alertsProperties.component"
                            :selected-values="selectedAlertComponents"
                            :label="__('Component')" :placeholder="__('Select components')"
                            @updated="sendPropertyUpdatedEvent" />
                        <AlertLockingDateFilter v-if="alertsProperties.lockingUser.length > 0" :range="alertLockingDateRange" @updated="updateAlertLockingDateRange" />
                        <AlertLockingUserFilter v-if="alertsProperties.lockingUser.length > 0" :values="alertsProperties.lockingUser"
                            :selected-values="selectedLockingUsers"
                            @updated="sendPropertyUpdatedEvent" />
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
                        <span>{{ __('Properties of UiPath Orchestrator connections') }}</span>
                    </div>
                    <div class="flex">
                        <span class="bg-gray-700 text-gray-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ orchestratorConnectionFiltersCount }}
                        </span>
                        <button v-if="orchestratorConnectionFiltersCount > 0" @click.stop="resetOrchestratorConnectionFilters()" type="button" class="inline-flex items-center p-0.5 ml-2 text-sm text-gray-400 bg-transparent rounded-sm hover:bg-gray-200 hover:text-gray-900" :aria-label="__('Remove')">
                            <svg aria-hidden="true" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="sr-only">{{ __('Remove') }}</span>
                        </button>
                    </div>
                </button>
            </h2>
            <div id="orchestrator-connection-properties-body" class="hidden" aria-labelledby="orchestrator-connection-properties-heading">
                <div class="p-2 border border-t-0 border-gray-200">
                    <div class="grid grid-cols-4 gap-4">
                        <OrchestratorConnectionFilter :values="orchestratorConnectionsProperties.self"
                            :selected-values="selectedOrchestratorConnections"
                            @updated="sendPropertyUpdatedEvent" />
                        <OrchestratorConnectionTenantFilter :values="orchestratorConnectionsProperties.tenants"
                            :selected-values="selectedOrchestratorConnectionTenants"
                            @updated="sendPropertyUpdatedEvent" />
                        <SimpleFieldFilter :values="orchestratorConnectionsProperties.hostingType"
                            :labels="orchestratorConnectionsProperties.hostingType.map(ht => ht === 'cloud' ? translate('Cloud') : translate('On-Premise'))"
                            :selected-values="selectedOrchestratorConnectionHostingTypes"
                            :label="__('Hosting type')" :placeholder="__('Select hosting types')"
                            @updated="sendPropertyUpdatedEvent" />
                        <SimpleFieldFilter :values="orchestratorConnectionsProperties.environmentType"
                            :labels="orchestratorConnectionsProperties.environmentType
                                .map(ht => ht.charAt(0).toUpperCase() + ht.slice(1))"
                            :selected-values="selectedOrchestratorConnectionEnvironmentTypes"
                            :label="__('Environment type')" :placeholder="__('Select environment types')"
                            @updated="sendPropertyUpdatedEvent" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>