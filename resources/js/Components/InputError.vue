<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-show="message">
            <p class="text-sm text-red-600">
                {{ message }}
            </p>
        </div>
    </transition>
</template>
