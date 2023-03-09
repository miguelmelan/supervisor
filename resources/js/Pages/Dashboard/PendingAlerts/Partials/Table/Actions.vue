<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
    item: Object,
    edit: Function,
    remove: Function,
});
</script>

<template>
    <Dropdown align="right" width="60">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 border border-gray-400 text-sm leading-4 font-medium rounded-md text-gray-neutral-55 bg-white hover:bg-gray-300 hover:text-gray-500 focus:outline-none focus:bg-gray-300 active:bg-gray-300 transition">
                    ...
                </button>
            </span>
        </template>
        <template #content>
            <div class="w-60">
                <DropdownLink as="a" href="#" target="blank">
                    {{ __('Action 1') }}
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </template>
                </DropdownLink>
            </div>
        </template>
    </Dropdown>
</template>