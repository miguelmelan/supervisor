<script setup>
import { computed, ref, watch } from 'vue';
import { debounce } from 'lodash';
import FormStep from '@/Components/FormStep.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TagsInput from '@/Components/TagsInput.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    mode: String,
    form: Object,
});

const name = computed(() => props.form.name);
const codeTouched = ref(false);

watch(name, debounce(function (value) {
    if (props.mode == 'create' && !codeTouched.value) {
        axios.post(route('configuration.generate-code'), {
            name: value,
        }).then(res => {
            props.form.code = res.data.code;
        });
    };
}, 500));
</script>
    
<template>
    <FormStep>
        <template #title>
            {{ __('Trigger details') }}
        </template>

        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('This section provides essential information about the AI-based alert trigger. It includes details such as the trigger\'s name, code, and associated tags. The name represents a descriptive title for the trigger, allowing users to easily identify and manage it. The code serves as a unique identifier for the trigger, helping to ensure proper tracking and referencing.') }}
                </p>
            </div>
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" :value="__('Name')" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" autocomplete="name"
                    required />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Code -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="code" :value="__('Code')" />
                <TextInput id="code" v-model="form.code" type="text" class="mt-1 block w-full" autocomplete="code"
                    required minlength="3" maxlength="5" @keypress="codeTouched = (form.code != '')" />
                <InputError :message="form.errors.code" class="mt-2" />
            </div>

            <!-- Tags -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="tags" :value="__('Tags')" />
                <TagsInput v-model="form.tags" />
                <InputError :message="form.errors.tags" class="mt-2" />
            </div>

            <!-- Type -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="type" :value="__('Type')" />
                <InputError :message="form.errors.environment_type" class="mt-2" />
                <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                    <button type="button"
                        class="relative px-4 py-3 inline-flex w-full rounded-lg rounded-b-none border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                        @click="form.type = 'automated_processes'">
                        <div :class="{'opacity-50': form.type && form.type != 'automated_processes'}">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 w-5 h-5 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                </svg>

                                <div class="text-sm text-gray-600"
                                    :class="{'font-semibold': form.type == 'automated_processes'}">
                                    {{ __('Automated business processes') }}
                                </div>

                                <svg v-if="form.type == 'automated_processes'" class="ml-2 h-5 w-5 text-green-400"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </button>
                    <button type="button"
                        class="relative px-4 py-3 inline-flex w-full rounded-lg rounded-t-none border-t border-gray-200 focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                        @click="form.type = 'orchestrator_connections'">
                        <div :class="{'opacity-50': form.type && form.type != 'orchestrator_connections'}">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="mr-2 w-5 h-5 text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                </svg>

                                <div class="text-sm text-gray-600"
                                    :class="{'font-semibold': form.type == 'orchestrator_connections'}">
                                    {{ __('UIPath Orchestrator entities') }}
                                </div>

                                <svg v-if="form.type == 'orchestrator_connections'" class="ml-2 h-5 w-5 text-green-400" fill="none"
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
    </FormStep>
</template>