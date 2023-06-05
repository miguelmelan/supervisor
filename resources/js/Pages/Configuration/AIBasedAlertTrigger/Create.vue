<script setup>
import { ref, inject, computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Actions from './Partials/Actions.vue';
import GeneralDetails from './Partials/GeneralDetails.vue';
import OrchestratorConnectionsSelectionDetails from './Partials/OrchestratorConnectionsSelection/Details.vue';
import RelatedEntitiesSelectionDetails from './Partials/RelatedEntitiesSelection/Details.vue';
import RulesDefinition from './Partials/RulesDefinition.vue';
import Scheduling from './Partials/Scheduling.vue';
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
    orchestrator_connections: [],
    conditions: '',
    recurrence: '',
    crons: [],
    classifications: [],
    verifications: [],
});

const relatedEntitiesSelected = computed(() => {
    if (form.orchestrator_connections.length > 0) {
        let shouldSkip = false;
        form.orchestrator_connections.forEach(oc => {
            if (shouldSkip) {
                return;
            }
            oc.orchestrator_connection.tenants.forEach(t => {
                if (
                    (t.selected_releases && t.selected_releases.length > 0)
                    || (t.selected_machines && t.selected_machines.length > 0)
                    || (t.selected_queues && t.selected_queues.length > 0)
                ) {
                    shouldSkip = true;
                    return;
                }
            });
        });
        return shouldSkip;
    }
    return false;
});

const cancelling = ref(false);

const breadcrumb = computed(() => {
    return [
        { href: route('configuration.index'), text: translate('Configuration') },
        { href: route('configuration.ai-based-alert-triggers.index'), text: translate("AI based alert triggers") },
        { text: translate('Create') },
    ];
});

const submit = () => {
    form.post(route('configuration.ai-based-alert-triggers.store'), {
        replace: true
    });
};
</script>

<template>
    <AppLayout :title="__('Configuration') + ' > ' + __('AI based alert triggers')">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Define an AI based alert trigger')" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <!-- general details -->
                        <GeneralDetails :form="form" mode="create" />

                        <!-- orchestrator connections selection -->
                        <OrchestratorConnectionsSelectionDetails :form="form" :orchestratorConnections="orchestratorConnections" mode="create" />

                        <!-- uipath entities selection -->
                        <div v-show="form.orchestrator_connections.length > 0">
                            <RelatedEntitiesSelectionDetails :form="form" mode="create" />
                        </div>

                        <div v-show="relatedEntitiesSelected">
                            <!-- rules definition -->
                            <RulesDefinition :form="form" mode="create" />

                            <!-- scheduling configuration -->
                            <Scheduling :form="form" mode="create" />
                        </div>

                        <!-- actions -->
                        <Actions :form="form" mode="create" :cancelling="cancelling" :related-entities-selected="relatedEntitiesSelected" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>