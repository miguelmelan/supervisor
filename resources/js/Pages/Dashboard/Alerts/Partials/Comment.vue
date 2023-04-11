<script setup>
import { inject } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const translate = inject('translate');

const props = defineProps({
    form: Object,
});
</script>

<template>
    <div class="w-full mt-8 border-t">
        <div class="py-2 bg-white">
            <label for="comment" class="sr-only">Your comment</label>
            <textarea id="comment" rows="4" class="w-full px-0 text-sm text-gray-900 bg-white border-0 not-resizable focus:ring-0" placeholder="Write a comment..." required></textarea>
        </div>
        <div class="flex items-center justify-between pt-4 border-t">
            <PrimaryButton @click.prevent="true">
                {{ __('Post comment') }}
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                </template>
            </PrimaryButton>
            <div class="flex">
                <button @click.prevent="true" type="button" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">{{ __('Attach file') }}</span>
                </button>
                <button @click.prevent="true" type="button" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">{{ __('Upload image') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>