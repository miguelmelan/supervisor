<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    href: String,
    as: String,
    active: {
        type: Boolean,
        default: false,
    },
    target: {
        type: String,
        default: ''
    },
    iconPosition: {
        type: String,
        default: 'left'
    },
    withBottomBorder: {
        type: Boolean,
        default: false,
    },
    withCheckbox: {
        type: Boolean,
        default: false,
    },
    checked: {
        type: Boolean,
        default: false,
    },
});

const goTo = (href) => {
    window.open(href, '_blank');
}
</script>

<template>
    <div>
        <div v-if="withCheckbox" class="flex items-center w-full px-4 py-2 text-sm leading-5 text-gray-neutral-55 text-left hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition">
            <input type="checkbox" :modelValue="checked" @update:modelValue="checked = $event"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
            <label class="w-full ml-2 text-sm font-medium text-gray-900 rounded" @click="checked = !checked">
                <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
                    <slot name="icon" />
                </div>
                <slot />
                <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
                    <slot name="icon" />
                </div>
            </label>
        </div>

        <button v-else-if="as == 'button'" type="submit"
            class="flex w-full px-4 py-2 text-sm leading-5 text-gray-neutral-55 text-left hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition">
            <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
                <slot name="icon" />
            </div>
            <slot />
            <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
                <slot name="icon" />
            </div>
        </button>

        <a v-else-if="as == 'a' && target == 'blank'" @click="goTo(href)" href="#"
            class="flex px-4 py-2 text-sm leading-5 text-gray-neutral-55 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition">
            <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
                <slot name="icon" />
            </div>
            <slot />
            <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
                <slot name="icon" />
            </div>
        </a>

        <a v-else-if="as == 'a'" :href="href"
            class="flex px-4 py-2 text-sm leading-5 text-gray-neutral-55 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition">
            <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
                <slot name="icon" />
            </div>
            <slot />
            <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
                <slot name="icon" />
            </div>
        </a>

        <Link v-else :href="href"
            class="flex px-4 py-2 text-sm leading-5 text-gray-neutral-55 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition"
            :class="active ? 'border-l-4 border-orange-50  text-orange-50 focus:border-orange-900': ''">
            <div v-if="$slots.icon && iconPosition == 'left'" class="mr-2 -ml-1 w-5 h-5">
                <slot name="icon" />
            </div>
            <slot />
            <div v-if="$slots.icon && iconPosition == 'right'" class="ml-2 -mr-1 w-5 h-5">
                <slot name="icon" />
            </div>
        </Link>
        <div v-if="withBottomBorder" class="hidden sm:block">
            <div class="py-0">
                <div class="border-t border-gray-200" />
            </div>
        </div>
    </div>
</template>
