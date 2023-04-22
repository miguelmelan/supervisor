<script setup>
import { inject, computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
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
    from: String,
});

let form = useForm({
    id: props.alert.id,
    id_padded: props.alert.id_padded,
    locked_at: props.alert.locked_at,
    locked_by: props.alert.locked_by,
    read_at: props.alert.read_at,
    read_by: props.alert.read_by,
    resolution_details: props.alert.resolution_details,
    automated_process_id: props.alert.automated_process_id,
    notification_name: props.alert.notification_name,
    data_: props.alert.data,
    component: props.alert.component,
    severity: props.alert.severity,
    creation_time: props.alert.creation_time,
    deep_link_relative_url: props.alert.deep_link_relative_url,
    false_positive: props.alert.false_positive === 1,
    tenant: props.alert.tenant,
    owned: props.alert.locked_by?.id === usePage().props.value.user.id,
    comments: props.alert.comments,
    mark_as_read: false,
});

const headerSubText = computed(() => {
    let text = form.read_at ? translate('Read by :username', {
        'username': form.owned ? translate('you') : form.read_by.name,
    }) : null;
    if (text) {
        return text;
    }
    return form.locked_at ? translate('Locked by :username', {
        'username': form.owned ? translate('you') : form.locked_by.name,
    }) : ''
});

const backLocation = route(props.from, {
    loadSavedSearch: true,
});
</script>

<template>
    <AppLayout :title="__('Edit alert #:id', {
            id: form.id_padded,
        })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Edit alert #:id', {
                    id: form.id_padded,
                })" :sub-text="headerSubText" :class="{
                    'bg-green-100 text-green-900': form.read_at,
                    'bg-yellow-100 text-yellow-900': !form.read_at && form.owned,
                    'bg-red-100 text-red-900': !form.read_at && form.locked_at && !form.owned,
                }" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <Details :form="form" />
                        <SectionBorder />

                        <ResolutionDetails :form="form" :original-resolution-details="alert.resolution_details"
                            :original-false-positive="alert.false_positive === 1" />
                        <SectionBorder />

                        <!-- <Extensions :form="form" />
                        <SectionBorder /> -->

                        <Timeline :form="form" />

                        <!-- actions -->
                        <Actions :form="form" mode="edit" :back-location="backLocation" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>