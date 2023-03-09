<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    href: {
        type: String,
    },
    iconPosition: {
        type: String,
        default: 'left'
    },
});
</script>

<template>
    <Link :href="href"
        class="inline-flex items-center justify-center px-4 py-2 bg-blue-50 border border-transparent rounded-md font-semibold text-white tracking-widest hover:bg-blue-500 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
            <slot name="icon" />
        </div>
        <slot />
        <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
            <slot name="icon" />
        </div>
    </Link>
</template>
