<script setup>
const props = defineProps({
    height: {
        type: String,
        default: 'sm',
    },
});
</script>

<template>
    <div class="hidden sm:block">
        <div class="py-4">
            <div class="border-t border-gray-200" :class="{
                'border-1': height === 'sm',
                'border-2': height === 'md',
                'border-3': height === 'l',
            }" />
        </div>
    </div>
</template>
