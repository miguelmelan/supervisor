<script setup>
import { computed } from 'vue';

const props = defineProps({
    verified: {
        type: Boolean,
        default: false,
    },
    verifiedAtForHumans: {
        type: String,
        default: null,
    },
    light: {
        type: Boolean,
        default: false,
    },
});

const verified = computed(() => props.verified);
const verifiedAtForHumans = computed(() => props.verifiedAtForHumans);
</script>

<template>
    <div class="flex items-center border p-2 rounded-md"
        :class="{
            'border-success-50 bg-green-200': verified,
            'border-warning-50 bg-orange-200': !verifiedAtForHumans,
            'border-error-50 bg-red-200': verifiedAtForHumans && !verified,
            'border-0 bg-transparent': light,
        }">
        <svg v-if="verified" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-6 h-6 text-success-50 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
        </svg>
        <svg v-else-if="!verifiedAtForHumans" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-warning-50 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-6 h-6 text-error-50 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        <span :class="{
            'text-green-800': verified
        }">
            {{ verifiedAtForHumans ?? __('Not verified yet') }}
        </span>
    </div>
</template>