<script setup>
import { inject, computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';

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
        { text: translate('View alert #:id', {
            id: form.id,
        }) },
    ];
});

</script>

<template>
    <AppLayout :title="__('Pending alerts') + ' > ' + __('Alert') + ` #${form.id}`">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('View alert #:id', {
                    id: form.id,
                })" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        The form...
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>