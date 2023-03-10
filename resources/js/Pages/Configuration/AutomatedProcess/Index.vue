<script setup>
import { computed, reactive, ref, watch, inject } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import 'flowbite';
import debounce from 'lodash/debounce';
import AppLayout from '@/Layouts/AppLayout.vue';
import BulkDeleteConfirmationModal from './Partials/BulkDeleteConfirmationModal.vue';
import BulkDeleteButtons from './Partials/BulkDeleteButtons.vue';
import DeleteConfirmationModal from './Partials/DeleteConfirmationModal.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryLink from '@/Components/PrimaryLink.vue';

const translate = inject('translate');

const props = defineProps({
    automatedProcesses: Object,
    filters: Object
});

const search = ref(props.filters.search);
const sorting = reactive(props.filters.sorting ?? {
    field: 'code',
    direction: 'asc',
});
const confirmingDeletion = ref(false);
const confirmingBulkDeletion = ref(false);
const selected = ref([]);

const form = useForm({});

const breadcrumb = computed(() => {
    return [
        { href: route("configuration.index"), text: translate("Configuration") },
        { text: translate("Automated business processes") },
    ];
});

watch(search, debounce(function (value) {
    let attributes = value ? { search: value } : {};
    attributes.sorting = sorting;
    Inertia.get(route('configuration.automated-processes.index'), attributes, {
        preserveState: true,
        replace: true,
    });
}, 300));

watch(sorting, function(value) {
    Inertia.get(route('configuration.automated-processes.index'), {
        sorting: {
            field: value.field,
            direction: value.direction,
        },
    }, {
        preserveScroll: true,
        preserveState: true,
    });
});

const sort = (field) => {
    if (sorting.field !== field) {
        sorting.direction = 'asc';
    } else {
        sorting.direction = sorting.direction === 'asc' ? 'desc' : 'asc';
    }
    sorting.field = field;
};

const edit = (item) => {
    Inertia.get(route('configuration.automated-processes.edit', {
        automated_process: item.id
    }));
}

const remove = (item) => {
    Object.assign(form, item);
    confirmingDeletion.value = true;
}

const selectAll = computed({
    get() {
        return props.automatedProcesses.data.length > 0 ? selected.value.length == props.automatedProcesses.data.length : false;
    },
    set(value) {
        let values = [];
        if (value) {
            props.automatedProcesses.data.forEach(function (automatedProcess) {
                values.push(automatedProcess.id);
            });
        }
        
        selected.value = values;
    }
});

const resetBulkDeletion = () => {
    confirmingBulkDeletion.value = false;
    selected.value = [];
};
</script>

<template>
    <AppLayout :title="__('Configuration') + ' > ' + __('Automated business processes')">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
        
        <div class="bg-white shadow-xl sm:rounded-lg">
            <PageContentHeader :text="__('Define automated business processes by linking them to entities in UiPath Orchestrator instances (robots, processes, queues, etc.).')">
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                    </svg>
                </template>
            </PageContentHeader>

            <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                <div class="flex justify-between">
                    <!-- search -->
                    <div v-show="selected.length == 0">
                        <label for="search" class="sr-only">{{ __('Search') }}</label>
                        <div class="relative mt-1">
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" aria-hidden="true" fill="currentColor"
                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input v-model="search" type="text" id="search"
                                class="block p-2 pl-10 w-80 text-sm border-gray-400 hover:border-gray-neutral-55 focus:border-blue-50 rounded-md"
                                :placeholder="__('Search')">
                            <button v-if="search" type="button" class="flex absolute inset-y-0 right-0 items-center pr-3" @click="search = ''">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-5 h-5 text-gray-400 hover:text-gray-neutral-55">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <BulkDeleteButtons :selected="selected" @confirm="confirmingBulkDeletion = true" @cancel="resetBulkDeletion" />
                    <PrimaryLink :href="route('configuration.automated-processes.create')">
                        {{ __('Define a business automated process') }}
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                            </svg>
                        </template>
                    </PrimaryLink>
                </div>

                <!-- table -->
                <div class="mt-6">
                    <table class="min-w-full divide-y divide-y-gray-200 text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="p-4">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="rounded text-blue-50 shadow-sm focus:border-blue-50"
                                            :class="{
                                                'bg-gray-100 border-gray-300 hover:border-gray-400': automatedProcesses.data.length == 0,
                                                'bg-white border-gray-400 hover:border-gray-neutral-55': automatedProcesses.data.length > 0
                                            }"
                                            v-model="selectAll" name="checkbox-all"
                                            :disabled="automatedProcesses.data.length == 0">
                                        <label for="checkbox-all" class="sr-only">{{ __('Select all items')}}</label>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-6">
                                    <Link @click="sort('code')" class="flex items-center">
                                        <span>{{ __('Code') }}</span>
                                        <span class="ml-2">
                                            <!-- up -->
                                            <svg v-if="sorting.field == 'code' && sorting.direction == 'asc'" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                            <!-- down -->
                                            <svg v-else-if="sorting.field == 'code' && sorting.direction == 'desc'" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                            <!-- up down -->
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </span>
                                    </Link>
                                </th>
                                <th scope="col" class="py-3 px-6">
                                    <Link @click="sort('name')" class="flex items-center">
                                        <span>{{ __('Name') }}</span>
                                        <span class="ml-2">
                                            <!-- up -->
                                            <svg v-if="sorting.field == 'name' && sorting.direction == 'asc'" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                            </svg>
                                            <!-- down -->
                                            <svg v-else-if="sorting.field == 'name' && sorting.direction == 'desc'" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                            <!-- up down -->
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </span>
                                    </Link>
                                </th>
                                <th scope="col" class="py-3 px-6">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in automatedProcesses.data" :key="item.id"
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
                                <td class="py-4 px-6">
                                    <Dropdown align="center" width="60">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-gray-400 text-sm leading-4 font-medium rounded-md text-gray-neutral-55 bg-white hover:bg-gray-300 hover:text-gray-500 focus:outline-none focus:bg-gray-300 active:bg-gray-300 transition">
                                                    ...
                                                </button>
                                            </span>
                                        </template>
                                        <template #content>
                                            <div class="w-60">
                                                <DropdownLink as="button" @click.prevent="edit(item)">
                                                    {{ __('Edit') }}
                                                    <template #icon>
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                        </svg>
                                                    </template>
                                                </DropdownLink>
                                                <DropdownLink as="button" @click.prevent="remove(item)">
                                                    {{ __('Delete') }}
                                                    <template #icon>
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                            stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                    </template>
                                                </DropdownLink>
                                            </div>
                                        </template>
                                    </Dropdown>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <BulkDeleteButtons class="mt-6" :selected="selected" @confirm="confirmingBulkDeletion = true" @cancel="resetBulkDeletion" />

                <!-- paginator -->
                <Pagination v-show="selected.length == 0" class="mt-6" :links="automatedProcesses.links"
                    :from="automatedProcesses.from" :to="automatedProcesses.to"
                    :total="automatedProcesses.total" />

            </div>
        </div>

        <DeleteConfirmationModal v-if="confirmingDeletion" :form="form" @close="confirmingDeletion = false" />
        <BulkDeleteConfirmationModal v-if="confirmingBulkDeletion" @close="resetBulkDeletion" :selected="selected" />
    </AppLayout>
</template>