<script setup>
defineProps({
    text: String,
});
</script>

<template>
    <div class="p-6 bg-white border-b border-gray-200">
        <!-- <div>
            <ApplicationLogo class="block h-12 w-auto" />
        </div> -->

        <div class="flex items-center">
            <div v-if="$slots.icon" class="mr-4 -ml-1 w-10 h-10">
                <slot name="icon" />
            </div>
            <div class="text-2xl flex-grow">{{ text }}</div>
        </div>
    </div>
</template>