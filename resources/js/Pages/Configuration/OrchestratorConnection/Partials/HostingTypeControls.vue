<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    form: Object,
    verification: Object,
});

const selectHostingType = (hostingType) => {
    props.form.hosting_type = hostingType;
};
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <InputLabel for="hosting-type" :value="__('Hosting Type')" />
        <InputError :message="form.errors.hosting_type" class="mt-2" />
        <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
            <button type="button"
                class="relative px-4 py-3 inline-flex w-full rounded-lg rounded-b-none border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                :class="{ 'opacity-25': form.processing || verification.processing }"
                :disabled="form.processing || verification.processing" @click="selectHostingType('cloud')">
                <div :class="{'opacity-50': form.hosting_type && form.hosting_type != 'cloud'}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 w-5 h-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
                        </svg>

                        <div class="text-sm text-gray-600" :class="{'font-semibold': form.hosting_type == 'cloud'}">
                            {{ __('Cloud') }}
                        </div>

                        <svg v-if="form.hosting_type == 'cloud'" class="ml-2 h-5 w-5 text-green-400" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-2 text-xs text-gray-600 text-left">
                        {{ __('Use this option when UiPath Orchestrator is hosted on Automation Cloud.') }}
                    </div>
                </div>
            </button>
            <button type="button"
                class="relative px-4 py-3 inline-flex w-full rounded-lg rounded-t-none border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                :class="{ 'opacity-25': form.processing || verification.processing }"
                :disabled="form.processing || verification.processing" @click="selectHostingType('on_premise')">
                <div :class="{'opacity-50': form.hosting_type && form.hosting_type != 'on_premise'}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 w-5 h-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>

                        <div class="text-sm text-gray-600"
                            :class="{'font-semibold': form.hosting_type == 'on_premise'}">
                            {{ __('On-Premise') }}
                        </div>

                        <svg v-if="form.hosting_type == 'on_premise'" class="ml-2 h-5 w-5 text-green-400" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-2 text-xs text-gray-600 text-left">
                        {{ __('Use this option when UiPath Orchestrator is hosted on-premise either in standalone mode or using Automation Suite.') }}
                    </div>
                </div>
            </button>
        </div>
    </div>
</template>