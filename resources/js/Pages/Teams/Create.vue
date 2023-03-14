<script setup>
import { computed, inject } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTeamForm from '@/Pages/Teams/Partials/CreateTeamForm.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const translate = inject('translate');

const breadcrumb = computed(() => {
    return [
        { text: translate("Create Team") },
    ];
});
</script>

<template>
    <AppLayout :title="__('Create Team')">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <CreateTeamForm />
            </div>
        </div>
    </AppLayout>
</template>
