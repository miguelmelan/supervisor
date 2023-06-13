<script setup>
import { ref, inject, computed } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Actions from './Partials/Actions.vue';
import AutomatedProcessesSelection from './Partials/AutomatedProcessesSelection.vue';
import GeneralDetails from './Partials/GeneralDetails.vue';
import OrchestratorConnectionsSelectionDetails from './Partials/OrchestratorConnectionsSelection/Details.vue';
import RelatedEntitiesSelectionDetails from './Partials/RelatedEntitiesSelection/Details.vue';
import RulesDefinition from './Partials/RulesDefinition.vue';
import Scheduling from './Partials/Scheduling.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';

const translate = inject('translate');

const props = defineProps({
    alertTrigger: Object,
    orchestratorConnections: Array,
    automatedProcesses: Array,
});

const form = useForm({
    id: props.alertTrigger.id,
    code: props.alertTrigger.code,
    name: props.alertTrigger.name,
    type: props.alertTrigger.type,
    tags: props.alertTrigger.tags,
    orchestrator_connections: props.alertTrigger.orchestratorConnections.map(orchestratorConnection => {
        props.alertTrigger.orchestratorConnectionTenants.forEach(tenant => {
            if (orchestratorConnection.tenants.find(current => current.id === tenant.id)) {
                const index = orchestratorConnection.tenants.map(item => item.id).indexOf(tenant.id);

                tenant.selected_releases = props.alertTrigger.releases
                    .filter(release => release.tenant_id === tenant.id)
                    .map(release => {
                        return {
                            id: release.external_id,
                            folder: release.external_folder_id,
                        };
                    });

                tenant.selected_machines = props.alertTrigger.machines
                    .filter(machine => machine.tenant_id === tenant.id)
                    .map(machine => {
                        return {
                            id: machine.external_id,
                            folder: machine.external_folder_id,
                        };
                    });

                tenant.selected_queues = props.alertTrigger.queues
                    .filter(queue => queue.tenant_id === tenant.id)
                    .map(queue => {
                        return {
                            id: queue.external_id,
                            folder: queue.external_folder_id,
                        };
                    });

                orchestratorConnection.tenants[index] = tenant;
            }
        });
        
        return {
            orchestrator_connection: orchestratorConnection,
            tenants: orchestratorConnection.tenants
                .filter(tenant => {
                    return props.alertTrigger.orchestratorConnectionTenants
                        .map(t => t.id)
                        .includes(tenant.id);
                })
                .map(item => item.id),
        };
    }),
    automated_processes: props.alertTrigger.automatedProcesses,
    conditions: props.alertTrigger.conditions,
    recurrence: props.alertTrigger.recurrence,
    crons: props.alertTrigger.crons,
    verifications: props.alertTrigger.verifications,
    look_back_buffer: props.alertTrigger.lookBackBuffer,
});

const relatedEntitiesSelected = computed(() => {
    if (form.type === 'orchestrator_connections') {
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
    } else if (form.type === 'automated_processes') {
        return form.automated_processes.length > 0;
    }
});

const updateSelectedAutomatedProcesses = (data) => {
    form.automated_processes = data;
};

const cancelling = ref(false);

const breadcrumb = computed(() => {
    return [
        { href: route('configuration.index'), text: translate('Configuration') },
        { href: route('configuration.ai-based-alert-triggers.index'), text: translate("AI based alert triggers") },
        { text: form.name },
    ];
});

const submit = () => {
    form.put(route('configuration.ai-based-alert-triggers.update', props.alertTrigger.id), {
        preserveScroll: true,
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
                <PageContentHeader :text="__('Edit an AI based alert trigger')" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <!-- general details -->
                        <GeneralDetails :form="form" mode="edit" />

                        <div v-if="form.type === 'orchestrator_connections'">
                            <!-- orchestrator connections selection -->
                            <OrchestratorConnectionsSelectionDetails :form="form" :orchestratorConnections="orchestratorConnections" mode="edit" />

                            <!-- uipath entities selection -->
                            <div v-show="form.orchestrator_connections.length > 0">
                                <RelatedEntitiesSelectionDetails :form="form" mode="edit" />
                            </div>
                        </div>
                        <div v-else-if="form.type === 'automated_processes'">
                            <!-- automated processes selection -->
                            <AutomatedProcessesSelection :form="form" mode="edit" :alert-trigger="alertTrigger" :automated-processes="automatedProcesses" @updated="updateSelectedAutomatedProcesses" />
                        </div>

                        <div v-show="relatedEntitiesSelected">
                            <!-- rules definition -->
                            <RulesDefinition :form="form" mode="edit" />

                            <!-- scheduling configuration -->
                            <Scheduling :form="form" mode="edit" />
                        </div>

                        <!-- actions -->
                        <Actions :form="form" mode="edit" :cancelling="cancelling" :related-entities-selected="relatedEntitiesSelected" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>