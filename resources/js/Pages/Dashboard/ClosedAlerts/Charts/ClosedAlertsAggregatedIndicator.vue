<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: String,
    title: String,
    color: {
        type: String,
        default: 'blue',
    },
    valueSize: {
        type: String,
        default: '4xl',
    },
});

const color = computed(() => props.color );
</script>

<template>
    <div>
        <div class="h-full overflow-auto">
            <div
                class="flex items-stretch justify-between p-4 w-full h-full rounded-lg border shadow-md"
                :class="{
                    'bg-blue-600 border-blue-900 hover:bg-blue-700': color === 'blue',
                    'bg-green-600 border-green-900 hover:bg-green-700': color === 'green'
                }">
                <div class="flex flex-col justify-between w-full text-right">
                    <span class="text-md text-gray-100">{{ title }}</span>
                    <span class="text-white font-bold"
                        :class="`text-${valueSize}`">{{ value ?? __('No data') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>