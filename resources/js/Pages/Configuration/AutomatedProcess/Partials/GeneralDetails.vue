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
            {{ __('Business automated process details') }}
        </template>

        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('This section provides comprehensive information about the specific Automated Business Process. It includes details such as the name, code, and associated tags. The name typically represents a descriptive title for the Automated Business Process, while the code represents a unique identifier or reference for the process. Tags can be used to categorize and organize based on different criteria, allowing for easy search and filtering.') }}
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
        </template>
    </FormStep>
</template>