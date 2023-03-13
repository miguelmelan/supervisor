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
    automatedProcess: Object,
    orchestratorConnections: Array,
});

const form = useForm({
    id: props.automatedProcess.id,
    code: props.automatedProcess.code,
    name: props.automatedProcess.name,
    tags: props.automatedProcess.tags,
    properties: [],
    orchestrator_connections: props.automatedProcess.orchestratorConnections.map(orchestratorConnection => {
        props.automatedProcess.orchestratorConnectionTenants.forEach(tenant => {
            if (orchestratorConnection.tenants.find(current => current.id === tenant.id)) {
                const index = orchestratorConnection.tenants.map(item => item.id).indexOf(tenant.id);

                tenant.selected_releases = props.automatedProcess.releases
                    .filter(release => release.tenant_id === tenant.id)
                    .map(release => release.external_id);

                tenant.selected_machines = props.automatedProcess.machines
                    .filter(machine => machine.tenant_id === tenant.id)
                    .map(machine => machine.external_id);

                tenant.selected_queues = props.automatedProcess.queues
                    .filter(queue => queue.tenant_id === tenant.id)
                    .map(queue => queue.external_id);

                orchestratorConnection.tenants[index] = tenant;
            }
        });
        
        return {
            orchestrator_connection: orchestratorConnection,
            tenants: orchestratorConnection.tenants
                .filter(tenant => {
                    return props.automatedProcess.orchestratorConnectionTenants
                        .map(t => t.id)
                        .includes(tenant.id);
                })
                .map(item => item.id),
        };
    }),
});

const cancelling = ref(false);

const breadcrumb = computed(() => {
    return [
        { href: route('configuration.index'), text: translate('Configuration') },
        { href: route('configuration.automated-processes.index'), text: translate("Automated business processes") },
        { text: form.name },
    ];
});

const submit = () => {
    form.put(route('configuration.automated-processes.update', props.automatedProcess.id), {
        preserveScroll: true,
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
                <PageContentHeader :text="__('Edit a business automated process')" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <!-- general details -->
                        <GeneralDetails :form="form" mode="edit" />

                        <!-- orchestrator connections selection -->
                        <OrchestratorConnectionsSelectionDetails :form="form"
                            :orchestratorConnections="orchestratorConnections" mode="edit" />

                        <!-- uipath processes selection -->
                        <div v-show="form.orchestrator_connections.length > 0">
                            <RelatedEntitiesSelectionDetails :form="form" mode="edit"
                                :automated-process="automatedProcess" />
                        </div>

                        <!-- uipath robots selection -->
                        <!-- uipath queues selection -->

                        <!-- actions -->
                        <Actions :form="form" mode="edit" :cancelling="cancelling" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>