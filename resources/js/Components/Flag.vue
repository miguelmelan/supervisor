<script setup>
defineProps({
    countryCode: String,
});
</script>
    
<template>
    <span class="fi" :class="['fi-' + countryCode]"></span>
</template>