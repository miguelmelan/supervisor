<script setup>
import { inject, computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import Actions from './Partials/Actions.vue';
import Details from './Partials/Details.vue';
import ResolutionDetails from './Partials/ResolutionDetails.vue';
import Extensions from './Partials/Extensions.vue';
import Timeline from './Partials/Timeline.vue';

const translate = inject('translate');

const props = defineProps({
    alert: Object,
});

let form = useForm({
    id: props.alert.id_padded,
});

const breadcrumb = computed(() => {
    return [
        { href: route('pending-alerts.index'), text: translate('Pending alerts') },
        { text: translate('Edit alert #:id', {
            id: form.id,
        }) },
    ];
});

</script>

<template>
    <AppLayout :title="__('Pending alerts') + ' > ' + __('Alert') + ` #${alert.id}`">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Edit alert #:id', {
                    id: form.id,
                })" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <Details :alert="alert" :form="form" />
                        <SectionBorder />

                        <ResolutionDetails :alert="alert" :form="form" />
                        <SectionBorder />

                        <Extensions :alert="alert" :form="form" />
                        <SectionBorder />

                        <Timeline :alert="alert" :form="form" />

                        <!-- actions -->
                        <Actions :form="form" mode="edit" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>