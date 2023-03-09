<script setup>
import 'flowbite';

const props = defineProps({
    id: String,
    level: {
        type: String,
        default: 'success',
    },
    message: String,
    closable: {
        type: Boolean,
        default: true,
    },
    textSize: {
        type: String,
        default: 'sm',
    },
});

const emit = defineEmits(['closed']);
</script>

<template>
    <div :id="`toast-${id}-${level}`" class="flex items-center p-3 w-full border text-gray-500 bg-white rounded-lg" role="alert"
        :class="{
            'border-green-100': level === 'success',
            'border-orange-100': level === 'warning',
            'border-red-100': level === 'error',
        }">
        <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 rounded-lg"
        :class="{
            'text-success-50 bg-green-100': level === 'success',
            'text-warning-50 bg-orange-100': level === 'warning',
            'text-error-50 bg-red-100': level === 'error',
        }">
            <svg v-if="level === 'success'" aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            <svg v-if="level === 'warning' || level === 'error'" aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        
        </div>
        <div class="ml-3 font-normal" :class="`text-${textSize}`">{{ message }}</div>
        <button v-if="closable" @click.prevent="emit('closed')" type="button" class="ml-auto mx-0 -my-1.5 text-gray-400 hover:text-gray-900 rounded-lg p-1.5 inline-flex h-8 w-8" :data-dismiss-target="`#toast-${id}-${level}`" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </button>
    </div>
</template>