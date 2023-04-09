<script setup>
import { inject } from 'vue';
import FormStep from '@/Components/FormStep.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Toggle from '@/Components/Toggle.vue';

const translate = inject('translate');

const props = defineProps({
    alert: Object,
    form: Object,
});
</script>

<template>
    <FormStep>
        <template #title>
            {{ __('Resolution details') }}
        </template>
        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('This section contains information about how the user resolved the alert. This includes a description of the actions taken to address the issue, any changes made to the system or process, and any additional notes or comments related to the resolution. The section serves as a record of the steps taken to resolve the alert, providing valuable information for future reference.') }}
                </p>
            </div>
        </template>
        <template #form>
            <div class="col-span-6">
                <div>
                    <label for="comment" class="sr-only">Describe all actions taken to resolve the alert</label>
                    <textarea id="comment" rows="10" class="w-full p-0 text-sm text-gray-900 bg-white border-0 not-resizable ring-0 focus:ring-0" placeholder="Write actions taken ..." required></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <PrimaryButton>
                            {{ __('Save') }}
                            <template #icon>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </template>
                        </PrimaryButton>
                        <Toggle class="ml-4" :checked="true" :label="__('Resolved ?')" />
                    </div>
                    <div class="flex pl-0 space-x-1 sm:pl-2">
                        <button type="button" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">{{ __('Attach file') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </FormStep>
</template>