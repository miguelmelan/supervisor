<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    inactive: {
        type: Boolean,
        default: false
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class=" focus:border-blue-50 rounded-md"
        :class="{ 'bg-gray-100 border-gray-300 hover:border-gray-400' : inactive, 'border-gray-400 hover:border-gray-neutral-55' : !inactive }"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
