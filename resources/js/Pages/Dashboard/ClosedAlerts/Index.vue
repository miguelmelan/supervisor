<script setup>
import { ref, onMounted, computed, watch, reactive, inject } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Filters from './Partials/Filters/Index.vue';
import NewAlertsReload from './Partials/NewAlertsReload.vue';
import Tiles from './Partials/Tiles.vue';
import Navbar from '../Navbar.vue';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import QueryString from 'qs';

const translate = inject('translate');
const sendNotification = inject('sendNotification');

const props = defineProps({
    alerts: Object,
    alertsByCategory: Object,
    alertsCount: Number,
    pendingAlertsCount: Number,
    automatedProcessesCount: Number,
    alertsAverageResolutionTimeEveryday: Object,
    alertsAverageResolutionTime: String,
    filters: Object,
    alertsProperties: Object,
    orchestratorConnectionsProperties: Object,
});

const pendingAlertsCount = ref(props.pendingAlertsCount);
const newPendingAlertsCount = ref(0);
const closedAlertsCount = ref(props.alertsCount);
const newClosedAlertsCount = ref(0);
const automatedProcessesCount = ref(props.automatedProcessesCount);

const sorting = reactive(props.filters.sorting ?? {
    field: 'read_at',
    direction: 'desc',
});
const filtersSelected = ref(false);
const filtersData = computed(() => {
    const data = props.filters.data;

    if (data) {
        filtersSelected.value = ('alert' in data || 'orchestratorConnection' in data);
        return {
            alert: {
                creationDateRange: data.alert ? data.alert.creationDateRange ?? [] : [],
                closingDateRange: data.alert ? data.alert.closingDateRange ?? [] : [],
                selectedClosingUsers: data.alert ? data.alert.selectedClosingUsers ?? [] : [],
                selectedSeverities: data.alert ? data.alert.selectedSeverities ?? [] : [],
                selectedNotificationNames: data.alert ? data.alert.selectedNotificationNames ?? [] : [],
                selectedComponents: data.alert ? data.alert.selectedComponents ?? [] : [],
            },
            orchestratorConnection: {
                selected: data.orchestratorConnection ? data.orchestratorConnection.selected ?? [] : [],
                selectedTenants: data.orchestratorConnection ? data.orchestratorConnection.selectedTenants ?? [] : [],
                selectedHostingTypes: data.orchestratorConnection ? data.orchestratorConnection.selectedHostingTypes ?? [] : [],
                selectedEnvironmentTypes: data.orchestratorConnection ? data.orchestratorConnection.selectedEnvironmentTypes ?? [] : [],
            },
        };
    }

    return {
        alert: {
            creationDateRange: [],
            closingDateRange: [],
            selectedClosingUsers: [],
            selectedSeverities: [],
            selectedNotificationNames: [],
            selectedComponents: [],
        },
        orchestratorConnection: {
            selected: [],
            selectedTenants: [],
            selectedHostingTypes: [],
            selectedEnvironmentTypes: [],
        },
    };
});

const form = useForm({});

watch(sorting, function (value) {
    Inertia.get(route('closed-alerts.index'), {
        sorting: {
            field: value.field,
            direction: value.direction,
        },
    }, {
        preserveScroll: true,
        preserveState: true,
    });
});

const filter = (event) => {
    form.processing = true;
    filtersSelected.value = event > 0;
    const alertFilters = filtersData.value.alert;
    let alertAttributes = alertFilters.creationDateRange ? { creationDateRange: alertFilters.creationDateRange } : {};
    if (alertFilters.closingDateRange) {
        alertAttributes.closingDateRange = alertFilters.closingDateRange;
    }
    if (alertFilters.selectedClosingUsers) {
        alertAttributes.selectedClosingUsers = alertFilters.selectedClosingUsers;
    }
    if (alertFilters.selectedSeverities) {
        alertAttributes.selectedSeverities = alertFilters.selectedSeverities;
    }
    if (alertFilters.selectedNotificationNames) {
        alertAttributes.selectedNotificationNames = alertFilters.selectedNotificationNames;
    }
    if (alertFilters.selectedComponents) {
        alertAttributes.selectedComponents = alertFilters.selectedComponents;
    }

    const orchestratorConnectionFilters = filtersData.value.orchestratorConnection;
    let orchestratorConnectionAttributes = {};
    if (orchestratorConnectionFilters.selected) {
        orchestratorConnectionAttributes.selected = orchestratorConnectionFilters.selected;
    }
    if (orchestratorConnectionFilters.selectedTenants) {
        orchestratorConnectionAttributes.selectedTenants = orchestratorConnectionFilters.selectedTenants;
    }
    if (orchestratorConnectionFilters.selectedHostingTypes) {
        orchestratorConnectionAttributes.selectedHostingTypes = orchestratorConnectionFilters.selectedHostingTypes;
    }
    if (orchestratorConnectionFilters.selectedEnvironmentTypes) {
        orchestratorConnectionAttributes.selectedEnvironmentTypes = orchestratorConnectionFilters.selectedEnvironmentTypes;
    }

    Inertia.get(route('closed-alerts.index'), {
        data: {
            alert: alertAttributes,
            orchestratorConnection: orchestratorConnectionAttributes,
        },
    }, {
        preserveScroll: true,
        preserveState: false,
        onFinish: () => {
            form.processing = false;
        },
    });
};

const sort = (field) => {
    if (sorting.field !== field) {
        sorting.direction = 'asc';
    } else {
        sorting.direction = sorting.direction === 'asc' ? 'desc' : 'asc';
    }
    sorting.field = field;
};

const open = (item) => {
    Inertia.get(route('alerts.edit', {
        alert: item.id,
    }));
};

const reload = () => {
    Inertia.visit(route('closed-alerts.index', {
        loadSavedSearch: true,
    }), {
        preserveScroll: true,
    });
};

Echo.channel('orchestrator-connection-tenant-alert')
    .listen('.new', (data) => {
        const alert = data.orchestratorConnectionTenantAlert;
        newPendingAlertsCount.value++;
        // sendNotification(translate('A new alert was created!'));
    })
    .listen('.closed', (data) => {
        const alert = data.orchestratorConnectionTenantAlert;
        if (alert.read_by.id !== usePage().props.value.user.id) {
            newClosedAlertsCount.value++;
        }
    });

onMounted(() => {
    setTimeout(() => {
        if (window.location.href.endsWith('/true')) {
            let qs = QueryString.stringify({
                data: filtersData.value,
            });
            if (qs) {
                window.history.replaceState(null, '', `/closed-alerts?${qs}`);
            } else {
                window.history.replaceState(null, '', '/closed-alerts');
            }
        }
    }, 500);
});
</script>

<template>
    <AppLayout :title="__('Dashboard') + ' > ' + __('Closed alerts')">
        <div class="bg-white shadow-xl sm:rounded-lg">
            <PageContentHeader :text="__('Supervise your digital workforce')">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-10 h-10 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                    </svg>
                </template>
            </PageContentHeader>

            <!-- Navbar -->
            <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                <Navbar :pending-alerts-count="pendingAlertsCount"
                    :closed-alerts-count="closedAlertsCount"
                    :new-pending-alerts-count="newPendingAlertsCount"
                    :new-closed-alerts-count="newClosedAlertsCount"
                    :closed-alerts-filters-selected="filtersSelected" />
            </div>

            <!-- Main content -->
            <div class="px-6 pb-6 sm:px-20 bg-gray-200 bg-opacity-25">
                <div class="grid grid-cols-5 gap-4 mb-4">
                    <!-- Tiles -->
                    <Tiles :closed-alerts-count="closedAlertsCount"  
                        :alerts-average-resolution-time-everyday="alertsAverageResolutionTimeEveryday"
                        :alerts-average-resolution-time="alertsAverageResolutionTime"
                        :alerts-by-category="alertsByCategory" />
                </div>

                <Filters :data="filtersData" 
                    :alerts-properties="alertsProperties"
                    :orchestrator-connections-properties="orchestratorConnectionsProperties"
                    @property-updated="filter"
                    class="mb-4" />

                <NewAlertsReload :show="newClosedAlertsCount > 0" :action="reload" />

                <table class="min-w-full divide-y divide-y-gray-200 text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="py-3 px-6">
                                <Link @click.prevent="sort('id')" class="flex items-center">
                                    <span>{{ __('ID') }}</span>
                                    <span class="ml-2">
                                        <!-- up -->
                                        <svg v-if="sorting.field == 'id' && sorting.direction == 'asc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                        <!-- down -->
                                        <svg v-else-if="sorting.field == 'id' && sorting.direction == 'desc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <!-- up down -->
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </span>
                                </Link>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <Link @click.prevent="sort('read_at')" class="flex items-center">
                                    <span>{{ __('Closing date') }}</span>
                                    <span class="ml-2">
                                        <!-- up -->
                                        <svg v-if="sorting.field == 'read_at' && sorting.direction == 'asc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                        <!-- down -->
                                        <svg v-else-if="sorting.field == 'read_at' && sorting.direction == 'desc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <!-- up down -->
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </span>
                                </Link>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Closed by') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Creation date') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Severity') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Type') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Component') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('UiPath Orchestrator connection') }}</span>
                            </th>
                            <th v-if="automatedProcessesCount > 0" scope="col" class="py-3 px-6">
                                <span>{{ __('Automated business process') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6 text-center">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in alerts.data" :key="item.id" class="bg-white border-b"
                            :class="{
                                'bg-yellow-100 text-yellow-900': item.read_by.id === $page.props.user.id,
                            }">
                            <th scope="row" class="py-4 px-6 font-semibold whitespace-nowrap">
                                {{ item.id_padded }}
                            </th>
                            <td class="py-4 px-6">
                                <span class="underline decoration-dashed cursor-pointer" :data-tooltip-target="'tooltip-closing-date-' + item.id">
                                    {{ item.read_at_for_humans }}
                                </span>
                                <div :id="'tooltip-closing-date-' + item.id" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{ item.read_at }}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                {{ item.read_by.name }}
                                <span v-if="$page.props.user.id === item.read_by.id">&nbsp;({{ __('you') }})</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="underline decoration-dashed cursor-pointer" :data-tooltip-target="'tooltip-creation-time-' + item.id">
                                    {{ item.creation_time_for_humans }}
                                </span>
                                <div :id="'tooltip-creation-time-' + item.id" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                    {{ item.creation_time }}
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
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
                                <dl class="max-w-md text-gray-900">
                                    <div class="flex flex-col pb-3">
                                        <dt class="mb-1 text-gray-500">{{ __('UiPath Orchestrator') }}</dt>
                                        <dd class="font-semibold">
                                            {{ item.tenant.orchestrator_connection.code }} -
                                            {{ item.tenant.orchestrator_connection.name }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-col pt-3">
                                        <dt class="mb-1 text-gray-500">{{ __('Tenant') }}</dt>
                                        <dd class="font-semibold">{{ item.tenant.name }}</dd>
                                    </div>
                                </dl>
                            </td>
                            <td v-if="automatedProcessesCount > 0" class="py-4 px-6">
                                <span v-if="item.automated_process">
                                    {{ item.automated_process.code }} -
                                    {{ item.automated_process.name }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex justify-center">
                                    <PrimaryButton type="button"
                                        @click.prevent="open(item)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
                                        </svg>
                                    </PrimaryButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="alerts.data.length > 0" class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="py-3 px-6">
                                <Link @click.prevent="sort('id')" class="flex items-center">
                                    <span>{{ __('ID') }}</span>
                                    <span class="ml-2">
                                        <!-- up -->
                                        <svg v-if="sorting.field == 'id' && sorting.direction == 'asc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                        <!-- down -->
                                        <svg v-else-if="sorting.field == 'id' && sorting.direction == 'desc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <!-- up down -->
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </span>
                                </Link>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <Link @click.prevent="sort('read_at')" class="flex items-center">
                                    <span>{{ __('Closing date') }}</span>
                                    <span class="ml-2">
                                        <!-- up -->
                                        <svg v-if="sorting.field == 'read_at' && sorting.direction == 'asc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                        <!-- down -->
                                        <svg v-else-if="sorting.field == 'read_at' && sorting.direction == 'desc'"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                        <!-- up down -->
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </span>
                                </Link>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Closed by') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Creation date') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Severity') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Type') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('Component') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6">
                                <span>{{ __('UiPath Orchestrator connection') }}</span>
                            </th>
                            <th v-if="automatedProcessesCount > 0" scope="col" class="py-3 px-6">
                                <span>{{ __('Automated business process') }}</span>
                            </th>
                            <th scope="col" class="py-3 px-6 text-center">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <!-- paginator -->
                <Pagination class="mt-4" :links="alerts.links"
                    :from="alerts.from" :to="alerts.to"
                    :total="alerts.total" />

                <NewAlertsReload :show="newPendingAlertsCount > 0" :action="reload" />
            </div>
        </div>
    </AppLayout>
</template>