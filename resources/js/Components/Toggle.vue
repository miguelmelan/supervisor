<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    id: {
        type: String,
        default: null
    },
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
        default: null,
    },
    label: {
        type: String,
        default: null
    },
    _disabled: {
        type: Boolean,
        default: false,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <div class="flex items-center">
        <label :for="id" class="inline-flex relative items-center cursor-pointer">
            <input v-model="proxyChecked" type="checkbox" :value="value" :id="id" class="sr-only peer" :disabled="_disabled">
            <div
                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"
                :class="{
                    'peer-checked:bg-blue-300': _disabled,
                }">
            </div>
            <span class="ml-3 text-sm font-medium text-gray-900">{{ label }}</span>
        </label>
    </div>
</template>