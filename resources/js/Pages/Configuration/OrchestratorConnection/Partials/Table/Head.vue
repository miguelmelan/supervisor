<script setup>
const props = defineProps({
    orchestratorConnections: Object,
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
                        'bg-gray-100 border-gray-300 hover:border-gray-400': orchestratorConnections.data.length == 0,
                        'bg-white border-gray-400 hover:border-gray-neutral-55': orchestratorConnections.data.length > 0
                    }" v-model="selectAll" name="checkbox-all"
                        :disabled="orchestratorConnections.data.length == 0">
                    <label for="checkbox-all" class="sr-only">{{ __('Select all items')}}</label>
                </div>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="sort('code')" class="flex items-center">
                    <span>{{ __('Code') }}</span>
                    <span class="ml-2">
                        <!-- up -->
                        <svg v-if="sorting.field == 'code' && sorting.direction == 'asc'"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                        <!-- down -->
                        <svg v-else-if="sorting.field == 'code' && sorting.direction == 'desc'"
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
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="sort('name')" class="flex items-center">
                    <span>{{ __('Name') }}</span>
                    <span class="ml-2">
                        <!-- up -->
                        <svg v-if="sorting.field == 'name' && sorting.direction == 'asc'"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                        <!-- down -->
                        <svg v-else-if="sorting.field == 'name' && sorting.direction == 'desc'"
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
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="sort('hosting_type')" class="flex items-center">
                    <span>{{ __('Hosting Type') }}</span>
                    <span class="ml-2">
                        <!-- up -->
                        <svg v-if="sorting.field == 'hosting_type' && sorting.direction == 'asc'"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                        <!-- down -->
                        <svg v-else-if="sorting.field == 'hosting_type' && sorting.direction == 'desc'"
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
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                <a href="#" @click="sort('environment_type')" class="flex items-center">
                    <span>{{ __('Environment Type') }}</span>
                    <span class="ml-2">
                        <!-- up -->
                        <svg v-if="sorting.field == 'environment_type' && sorting.direction == 'asc'"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                        </svg>
                        <!-- down -->
                        <svg v-else-if="sorting.field == 'environment_type' && sorting.direction == 'desc'"
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
                </a>
            </th>
            <th scope="col" class="py-3 px-6">
                {{ __('Verification Status') }}
            </th>
            <th scope="col" class="py-3 px-6">
                {{ __('Actions') }}
            </th>
        </tr>
    </thead>
</template>