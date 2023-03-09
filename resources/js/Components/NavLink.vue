<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    iconPosition: {
        type: String,
        default: 'left'
    },
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center px-1 pt-1 border-b-4 border-orange-50 font-medium leading-5 text-orange-50 focus:outline-none focus:border-orange-900 transition'
        : 'inline-flex items-center px-1 pt-1 border-b-4 border-transparent font-medium leading-5 text-gray-neutral-55 hover:text-gray-500 hover:border-gray-300 focus:outline-none focus:text-gray-900 focus:border-gray-900 transition';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
            <slot name="icon" />
        </div>
        <slot />
        <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
            <slot name="icon" />
        </div>
    </Link>
</template>
