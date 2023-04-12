<script setup>
import 'flowbite';
import { inject, onMounted } from 'vue';
import FormStep from '@/Components/FormStep.vue';
import Comment from './Comment.vue';

const translate = inject('translate');

const props = defineProps({
    form: Object,
});

onMounted(() => {
    document.querySelectorAll('[data-tooltip-target^="tooltip"]').forEach(trigger => {
        const target = document.getElementById(trigger.getAttribute('data-tooltip-target'));
        new Tooltip(target, trigger, {
            placement: 'left',
        });
    });
});
</script>

<template>
    <FormStep>
        <template #title>
            {{ __('Timeline') }}
        </template>
        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('This section provides a chronological view of all actions taken on an alert, from the time it was created to the time it was resolved. This section serves as a historical record of the alert\'s lifecycle, allowing users to track its progress and understand what actions were taken and when.') }}
                </p>
            </div>
        </template>
        <template #form>
            <div class="col-span-6">
                <ol class="relative border-l border-gray-200">                  
                    <!-- <li class="mb-10 ml-6">            
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:bg-gray-700 dark:border-gray-600">
                            <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">just now</time>
                            <div class="text-sm font-normal text-gray-500 dark:text-gray-300">Bonnie moved <a href="#" class="font-semibold text-blue-600 dark:text-blue-500 hover:underline">Jese Leos</a> to <span class="bg-gray-100 text-gray-800 text-xs font-normal mr-2 px-2.5 py-0.5 rounded dark:bg-gray-600 dark:text-gray-300">Funny Group</span></div>
                        </div>
                    </li>
                    <li class="mb-10 ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                            <div class="items-center justify-between mb-3 sm:flex">
                                <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">2 hours ago</time>
                                <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">Thomas Lean commented on  <a href="#" class="font-semibold text-gray-900 dark:text-white hover:underline">Flowbite Pro</a></div>
                            </div>
                            <div class="p-3 text-xs italic font-normal text-gray-500 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">Hi ya'll! I wanted to share a webinar zeroheight is having regarding how to best measure your design system! This is the second session of our new webinar series on #DesignSystems discussions where we'll be speaking about Measurement.</div>
                        </div>
                    </li> -->
                    <li class="ml-6" v-for="(comment, index) in form.comments" :class="{ 'mt-10': index > 0}">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white"
                            :data-tooltip-target="'tooltip-default-' + comment.id">
                            <img v-if="$page.props.jetstream.managesProfilePhotos"
                                class="h-8 w-8 rounded-full object-cover"
                                :src="comment.user.profile_photo_url" :alt="comment.user.name">
                            <svg v-else-if="comment.user.name !== 'system'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <svg v-else-if="comment.user.name === 'system'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" />
                            </svg>
                        </span>
                        <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex">
                            <time class="flex flex-col text-right mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0 w-2/6">
                                <span>{{ comment.created_at_for_humans }}</span>
                                <span>{{ comment.created_at }}</span>
                            </time>
                            <div class="text-sm font-normal text-gray-500 lex w-4/6">
                                {{ comment.comment }}
                            </div>
                        </div>
                        <div :id="'tooltip-default-' + comment.id" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            {{ comment.user.name }}
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </li>
                </ol>
                
                <Comment :form="form" />
            </div>
        </template>
    </FormStep>
</template>