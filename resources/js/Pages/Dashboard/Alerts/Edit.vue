<script setup>
import { inject, computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
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
    id: props.alert.id,
    id_padded: props.alert.id_padded,
    locked_at: props.alert.locked_at,
    locked_by: props.alert.locked_by,
    read_at: props.alert.read_at,
    resolution_details: props.alert.resolution_details,
    automated_process_id: props.alert.automated_process_id,
    notification_name: props.alert.notification_name,
    data_: props.alert.data,
    component: props.alert.component,
    severity: props.alert.severity,
    creation_time: props.alert.creation_time,
    deep_link_relative_url: props.alert.deep_link_relative_url,
    tenant: props.alert.tenant,
    owned: props.alert.locked_at && props.alert.locked_by.id === usePage().props.value.user.id,
});

const breadcrumb = computed(() => {
    return [
        { href: route('pending-alerts.index'), text: translate('Pending alerts') },
        { text: translate('Edit alert #:id', {
            id: form.id_padded,
        }) },
    ];
});
</script>

<template>
    <AppLayout :title="__('Pending alerts') + ' > ' + __('Alert') + ` #${form.id_padded}`">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Edit alert #:id', {
                    id: form.id_padded,
                })" :sub-text="form.locked_at ? __('Locked by :username', {
                        'username': form.owned ? __('you') : form.locked_by.name,
                }) : ''" :class="{
                    'bg-yellow-100 text-yellow-900': form.owned,
                    'bg-red-100 text-red-900': form.locked_at && !form.owned,
                }" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <Details :form="form" />
                        <SectionBorder />

                        <ResolutionDetails :form="form" :original="alert.resolution_details" />
                        <!-- <SectionBorder />

                        <Extensions :form="form" />
                        <SectionBorder />

                        <Timeline :form="form" /> -->

                        <!-- actions -->
                        <Actions :form="form" mode="edit" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>