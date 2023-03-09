<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    active: Boolean,
    href: String,
    as: String,
});

const classes = computed(() => {
    return props.active
        ? 'block pl-3 pr-4 py-2 border-l-4 border-orange-50 text-base font-medium text-orange-50 focus:outline-none focus:border-orange-900 transition'
        : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-neutral-55 hover:text-gray-500 hover:border-gray-300 focus:outline-none focus:text-gray-900 focus:border-gray-900 transition';
});
</script>

<template>
    <div>
        <button v-if="as == 'button'" :class="classes" class="w-full text-left">
            <slot />
        </button>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
