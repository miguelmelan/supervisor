<script setup>
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
</script>

<template>
    <div>
        <div class="h-full overflow-auto">
            <div
                class="flex items-stretch justify-between p-4 w-full h-full rounded-lg border shadow-md "
                :class="`bg-${color}-50 border-${color}-900 hover:bg-${color}-700`">
                <div class="flex flex-col justify-between w-full text-right">
                    <span class="text-md text-gray-100">{{ title }}</span>
                    <span class="text-white font-bold"
                        :class="`text-${valueSize}`">{{ value ?? __('No data') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>