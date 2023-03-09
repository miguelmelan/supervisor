<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    form: Object,
});
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <InputLabel for="environment-type" :value="__('Environment Type')" />
        <InputError :message="form.errors.environment_type" class="mt-2" />
        <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
            <button type="button"
                class="relative px-4 py-3 inline-flex w-full rounded-lg rounded-b-none border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                @click="form.environment_type = 'development'">
                <div :class="{'opacity-50': form.environment_type && form.environment_type != 'development'}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 w-5 h-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                        </svg>

                        <div class="text-sm text-gray-600"
                            :class="{'font-semibold': form.environment_type == 'development'}">
                            {{ __('Development') }}
                        </div>

                        <svg v-if="form.environment_type == 'development'" class="ml-2 h-5 w-5 text-green-400"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </button>
            <button type="button"
                class="relative px-4 py-3 inline-flex w-full border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                @click="form.environment_type = 'test'">
                <div :class="{'opacity-50': form.environment_type && form.environment_type != 'test'}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 w-5 h-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                        </svg>

                        <div class="text-sm text-gray-600" :class="{'font-semibold': form.environment_type == 'test'}">
                            {{ __('Test') }}
                        </div>

                        <svg v-if="form.environment_type == 'test'" class="ml-2 h-5 w-5 text-green-400" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </button>
            <button type="button"
                class="relative px-4 py-3 inline-flex w-full border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                @click="form.environment_type = 'production'">
                <div :class="{'opacity-50': form.environment_type && form.environment_type != 'production'}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 w-5 h-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                        </svg>

                        <div class="text-sm text-gray-600"
                            :class="{'font-semibold': form.environment_type == 'production'}">
                            {{ __('Production') }}
                        </div>

                        <svg v-if="form.environment_type == 'production'" class="ml-2 h-5 w-5 text-green-400"
                            fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </button>
            <button type="button"
                class="relative px-4 py-3 inline-flex w-full rounded-lg rounded-t-none border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                @click="form.environment_type = 'hybrid'">
                <div :class="{'opacity-50': form.environment_type && form.environment_type != 'hybrid'}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="mr-2 w-5 h-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>

                        <div class="text-sm text-gray-600"
                            :class="{'font-semibold': form.environment_type == 'hybrid'}">
                            {{ __('Hybrid') }}
                        </div>

                        <svg v-if="form.environment_type == 'hybrid'" class="ml-2 h-5 w-5 text-green-400" fill="none"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </button>
        </div>
    </div>
</template>