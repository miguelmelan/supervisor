<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    links: Array,
    from: Number,
    to: Number,
    total: Number
});
</script>
    
<template>
    <div>
        <nav class="flex justify-between items-center" aria-label="Table navigation">
            <span v-if="total > 0" class="text-xs font-normal text-gray-500">
                {{ from }}-{{ to }} / {{ total }}
            </span>
            <span v-else class="text-sm font-normal text-gray-500" v-html="__('No result')"></span>
            <div>
                <template v-for="link in links">
                    <Link v-if="link.url" :href="link.url" v-html="link.label"
                        class="py-2 px-3 leading-tight text-xs text-gray-500 border"
                        :class="{
                            'bg-blue-50 text-white border-transparent hover:bg-blue-500 hover:text-gray-200' : link.active,
                            'bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700': !link.active
                        }"
                        preserve-scroll  preserve-state />
                    <span v-else v-html="link.label"
                        class="py-2 px-3 leading-tight text-xs text-gray-500 bg-white border border-gray-200" />
                </template>
            </div>
        </nav>
    </div>
</template>
