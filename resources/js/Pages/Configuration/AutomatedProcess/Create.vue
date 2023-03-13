<script setup>
import { ref, inject, computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Actions from './Partials/Actions.vue';
import GeneralDetails from './Partials/GeneralDetails.vue';
import OrchestratorConnectionsSelectionDetails from './Partials/OrchestratorConnectionsSelection/Details.vue';
import RelatedEntitiesSelectionDetails from './Partials/RelatedEntitiesSelection/Details.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';

const translate = inject('translate');

const props = defineProps({
    orchestratorConnections: Array,
});

const form = useForm({
    code: '',
    name: '',
    tags: [],
    properties: [],
    orchestrator_connections: [],
});

const cancelling = ref(false);

const breadcrumb = computed(() => {
    return [
        { href: route('configuration.index'), text: translate('Configuration') },
        { href: route('configuration.automated-processes.index'), text: translate("Automated business processes") },
        { text: translate('Create') },
    ];
});

const submit = () => {
    form.post(route('configuration.automated-processes.store'), {
        replace: true
    });
};
</script>

<template>
    <AppLayout :title="__('Configuration') + ' > ' + __('Automated business processes')">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Define a business automated process')" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <!-- general details -->
                        <GeneralDetails :form="form" mode="create" />

                        <!-- orchestrator connections selection -->
                        <OrchestratorConnectionsSelectionDetails :form="form" :orchestratorConnections="orchestratorConnections" mode="create" />

                        <!-- uipath processes selection -->
                        <div v-show="form.orchestrator_connections.length > 0">
                            <RelatedEntitiesSelectionDetails :form="form" mode="create" />
                        </div>
                        
                        <!-- uipath robots selection -->
                        <!-- uipath queues selection -->

                        <!-- actions -->
                        <Actions :form="form" mode="create" :cancelling="cancelling" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>