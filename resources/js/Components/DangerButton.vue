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
    <button :type="type" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition">
        <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
            <slot name="icon" />
        </div>
        <slot />
        <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
            <slot name="icon" />
        </div>
    </button>
</template>
