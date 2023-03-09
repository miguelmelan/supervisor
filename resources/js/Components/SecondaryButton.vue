<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    iconPosition: {
        type: String,
        default: 'left'
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-blue-50 tracking-widest shadow-sm hover:text-blue-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-blue-900 active:bg-gray-50 disabled:opacity-25 transition">
        <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
            <slot name="icon" />
        </div>
        <slot />
        <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
            <slot name="icon" />
        </div>
    </button>
</template>
