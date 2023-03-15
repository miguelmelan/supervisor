<script setup>
import 'flowbite';
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';

const show = ref(true);
const style = computed(() => usePage().props.value.flash?.toastStyle || 'success');
const message = computed(() => usePage().props.value.flash?.toast || '');
const id = computed(() => usePage().props.value.flash.toastId || '');

watch(id, async () => {
    show.value = true;

    setTimeout(() => {
        show.value = false;
    }, 5000);
});

setTimeout(() => {
    show.value = false;
}, 5000);
</script>

<template>
    <div class="fixed bottom-5 right-5">
        <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="show && message" class="flex items-center p-3 w-full border bg-white rounded-lg" role="alert"
                :class="{
                    'border-green-400': style === 'success',
                    'border-yellow-400': style === 'warning',
                    'border-red-400': style === 'error',
                    'bg-green-100': style === 'success',
                    'bg-yellow-100': style === 'warning',
                    'bg-red-100': style === 'error',
                    'text-green-900': style === 'success',
                    'text-yellow-900': style === 'warning',
                    'text-red-900': style === 'error',
                }">
                <div class="inline-flex flex-shrink-0 justify-center items-center w-8 h-8 rounded-lg"
                :class="{
                    'text-success-50 bg-green-100': style === 'success',
                    'text-warning-50 bg-yellow-100': style === 'warning',
                    'text-error-50 bg-red-100': style === 'error',
                }">
                    <svg v-if="style === 'success'" aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-if="style === 'warning' || style === 'error'" aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                
                </div>
                <div class="ml-2 font-normal text-sm">{{ message }}</div>
                <button @click.prevent="show = false" type="button" class="ml-auto mx-0 -my-1.5 text-gray-400 hover:text-gray-900 rounded-lg p-1.5 inline-flex h-8 w-8" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </transition>
    </div>
</template>