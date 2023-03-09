<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import SecondaryButton from './SecondaryButton.vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    height: {
        type: String,
        default: 'full',
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'bg-white'],
    },
    stayOpened: {
        type: Boolean,
        default: false,
    },
});

let open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        '48': 'w-48',
        'full': 'w-full',
    }[props.width.toString()];
});

const heightClass = computed(() => {
    return {
        '96': 'h-96 overflow-auto',
        'full': '',
    }[props.height.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0';
    }

    if (props.align === 'right') {
        return 'origin-top-right right-0';
    }

    return 'origin-top';
});
</script>

<template>
    <div class="relative">
        <div @click="open = ! open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-40" @click="open = stayOpened" />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 rounded-md shadow-lg"
                :class="[widthClass, heightClass, alignmentClasses]"
                style="display: none;"
                @click="open = stayOpened"
            >
                <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                    <slot name="content" />

                    <div v-if="stayOpened" class="flex justify-center items-center w-full px-4 py-2 text-sm leading-5 text-gray-neutral-55 text-left focus:outline-none focus:bg-gray-200 transition">
                        <SecondaryButton class="ml-2" type="button" @click.stop="open = false">
                            <template #icon>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </template>
                            {{ __('Close') }}
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
