<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSelector from '@/Components/LanguageSelector.vue';
</script>
    
<template>
    <footer class="sm:h-60 p-4 bg-white md:px-6 md:py-8">
        <div class="flex flex-col sm:flex-row items-center sm:justify-between">
            <ApplicationLogo class="block h-6" />
            <LanguageSelector class="mt-4 sm:mt-0"></LanguageSelector>
        </div>
    </footer>

</template>