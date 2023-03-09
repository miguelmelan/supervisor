<script setup>
import Dropdown from './Dropdown.vue';
import DropdownLink from './DropdownLink.vue';
import Flag from './Flag.vue';
</script>
        
<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                    <Flag :countryCode="$page.props.locale.flag"></Flag>
                    <span class="ml-2">{{ $page.props.locale.lang }}</span>

                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <DropdownLink v-if="$page.props.locale.code !== 'en'" :href="route('locale', 'en')">
                <Flag countryCode="gb"></Flag>
                <span class="ml-2">English</span>
            </DropdownLink>
            <DropdownLink v-if="$page.props.locale.code !== 'fr'" :href="route('locale', 'fr')">
                <Flag countryCode="fr"></Flag>
                <span class="ml-2">Français</span>
            </DropdownLink>
            <DropdownLink v-if="$page.props.locale.code !== 'ro'" :href="route('locale', 'ro')">
                <Flag countryCode="ro"></Flag>
                <span class="ml-2">Română</span>
            </DropdownLink>
        </template>
    </Dropdown>
</template>