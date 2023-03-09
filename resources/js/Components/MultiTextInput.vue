<script setup>
import { onBeforeMount, ref, watch } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    modelValue: Array,
    _key: {
        type: String,
        default: '_key',
    },
    _required: {
        type: Boolean,
        default: false,
    },
    id: String,
    model: {
        type: Object,
        default: {},
    },
    buttonsDisabled: {
        type: Boolean,
        default: false,
    },
});

const inputs = ref([]);

const emit = defineEmits(['update:modelValue']);

const updateValue = () => {
    emit('update:modelValue', inputs);
};

const deleteValue = (index) => {
    inputs.value.splice(index, 1);
    emit('update:modelValue', inputs);
};

const addValue = () => {
    inputs.value.push(JSON.parse(JSON.stringify(props.model)));
    emit('update:modelValue', inputs);
};

onBeforeMount(() => {
    inputs.value = props.modelValue;
});

</script>

<template>
    <div v-for="(item, index) in inputs">
        <div class="mt-2 flex items-center">
            <TextInput :id="id + '[' + index + ']'" v-model="item[_key]" type="text"
                class="block w-full flex-grow" :autocomplete="id + '[' + index + ']'" 
                :required="_required" @blur="updateValue()" />
            <DangerButton type="button" @click="deleteValue(index)" class="ml-2"
                :class="{ 'opacity-25': buttonsDisabled }"
                :disabled="buttonsDisabled">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </DangerButton>
        </div>
    </div>
    <SecondaryButton type="button" @click="addValue()" class="mt-2"
        :class="{ 'opacity-25': buttonsDisabled }"
        :disabled="buttonsDisabled">
        {{ __('Add') }}
        <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </template>
    </SecondaryButton>
</template>