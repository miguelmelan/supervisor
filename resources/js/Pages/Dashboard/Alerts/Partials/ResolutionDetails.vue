<script setup>
import { inject, computed, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormStep from '@/Components/FormStep.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Toggle from '@/Components/Toggle.vue';

const translate = inject('translate');

const props = defineProps({
    form: Object,
    originalResolutionDetails: String,
    originalFalsePositive: Boolean,
});

/* const emit = defineEmits([ 'file-prepared' ]);

const file = ref(null);
const fileName = computed(() => file.value?.name);
const fileExtension = computed(() => fileName.value?.substr(fileName.value?.lastIndexOf(".") + 1));
const fileMimeType = computed(() => file.value?.type); */

const dirty = computed(() => {
    return props.form.resolution_details !== props.originalResolutionDetails || props.form.false_positive !== props.originalFalsePositive;
});

const recentlyUpdated = ref(false);
const update = () => {
    props.form.processing = true;
    Inertia.post(route('alerts.update.resolution-details', props.form.id), props.form, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            props.form.processing = false;
            recentlyUpdated.value = true;
            setTimeout(() => {
                recentlyUpdated.value = false;
            }, 10000);
        },
    });
};

/* const chooseFiles = () => {
    document.getElementById('fileUpload').click();
};

const prepareFile = (e) => {
    file.value = e.target.files[0];
    emit('file-prepared', file);
}; */
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
            <div v-if="!form.read_at && form.locked_at && form.owned" class="col-span-6">
                <div>
                    <label for="comment" class="sr-only">Describe all actions taken to resolve the alert</label>
                    <textarea v-model="form.resolution_details" id="comment"
                        rows="10" class="w-full p-0 text-sm text-gray-900 bg-white border-0 not-resizable ring-0 focus:ring-0"
                        :placeholder="__('Write actions taken...')" required
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"></textarea>
                </div>
                <ActionMessage :on="recentlyUpdated" :class="{ 'mb-4': recentlyUpdated }">
                    {{ __('Saved.') }}
                </ActionMessage>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <PrimaryButton @click.prevent="update"
                            :class="{ 'opacity-25': form.processing || !dirty }"
                            :disabled="form.processing || !dirty">
                            {{ __('Save') }}
                            <template #icon>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </template>
                        </PrimaryButton>
                        <div class="flex flex-col ml-4">
                            <Toggle class="mb-2" v-model:checked="form.mark_as_read" :label="__('Mark the alert as read?')"
                                :class="{ 'opacity-25': form.processing || !dirty }"
                                :_disabled="form.processing || !dirty" />
                            <Toggle v-model:checked="form.false_positive" :label="__('False positive?')"
                                :class="{ 'opacity-25': form.processing }"
                                :_disabled="form.processing" />
                        </div>
                    </div>
                    <!-- <div class="flex items-center">
                        <input id="fileUpload" @change="prepareFile" type="file" hidden>
                        <div class="text-xs" v-if="file">{{ fileName }}</div>
                        <button @click.prevent="chooseFiles()" type="button" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 ml-2">
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg>
                            <span class="sr-only">{{ __('Attach file') }}</span>
                        </button>
                    </div> -->
                </div>
            </div>
            <div v-else class="col-span-6">
                <div class="w-full bg-white">
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200">
                            <li class="flex">
                                <div class="flex items-center space-x-4 w-full p-4">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ __('Resolution details') }}
                                        </p>
                                        <p v-if="form.resolution_details" class="text-sm text-gray-500">
                                            {{ __(form.resolution_details) }}
                                        </p>
                                        <p v-else class="text-sm text-gray-500 truncate">{{ __('Empty') }}</p>
                                    </div>
                                </div>
                            </li>
                            <li v-if="form.read_at" class="flex">
                                <div class="flex items-center space-x-4 w-full p-4">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M10.5 8.25h3l-3 4.5h3" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ __('False positive?') }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ __(form.false_positive ? 'Yes' : 'No') }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </template>
    </FormStep>
</template>