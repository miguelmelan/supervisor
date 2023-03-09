<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    items: Array,
});
</script>

<template>
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li v-for="(item, index) in items" :class="{ 'inline-flex items-center': index != items.length - 1 }" :aria-current="index == items.length - 1 ? 'page' : ''">
                <Link v-if="index != items.length - 1" :href="item.href" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                    <!-- > icon -->
                    <svg v-if="index > 0" class="w-6 h-6 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ item.text }}
                </Link>

                <div v-else class="flex items-center">
                    <svg v-if="index > 0" class="w-6 h-6 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-500">{{ item.text }}</span>
                </div>
            </li>
        </ol>
    </nav>
</template>