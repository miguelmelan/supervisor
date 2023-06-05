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
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Non beatae, aspernatur incidunt, hic vel ea
                    modi reiciendis cumque eos itaque numquam suscipit exercitationem earum voluptates illo. Vero
                    laborum illo id.
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