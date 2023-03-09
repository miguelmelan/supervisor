<script setup>
import { computed, onMounted, onUpdated, reactive, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Actions from './Partials/Actions.vue';
import ElasticSearchDetails from './Partials/ElasticSearchDetails.vue';
import KibanaDetails from './Partials/KibanaDetails.vue';
import OrchestratorDetails from './Partials/OrchestratorDetails.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';
import ConnectionDetails from './Partials/Connection/Details.vue';

const props = defineProps({
    orchestratorConnection: Object
});

let form = useForm({
    id: props.orchestratorConnection.id,
    code: props.orchestratorConnection.code,
    name: props.orchestratorConnection.name,
    hosting_type: props.orchestratorConnection.hosting_type,
    url: props.orchestratorConnection.url,
    organization_name: props.orchestratorConnection.organization_name,
    tenants: props.orchestratorConnection.tenants,
    environment_type: props.orchestratorConnection.environment_type,
    client_id: props.orchestratorConnection.client_id,
    client_secret: props.orchestratorConnection.client_secret,
    elasticsearch_enabled: props.orchestratorConnection.elasticsearch_enabled,
    elasticsearch_index_configuration: props.orchestratorConnection.elasticsearch_index_configuration,
    elasticsearch_url: props.orchestratorConnection.elasticsearch_url,
    elasticsearch_anonymous_authentication: props.orchestratorConnection.elasticsearch_anonymous_authentication,
    elasticsearch_username: props.orchestratorConnection.elasticsearch_username,
    elasticsearch_password: props.orchestratorConnection.elasticsearch_password,
    kibana_enabled: props.orchestratorConnection.kibana_enabled,
    kibana_url: props.orchestratorConnection.kibana_url,
    tags: props.orchestratorConnection.tags,
    verified: props.orchestratorConnection.verified,
    verified_at_for_humans: props.orchestratorConnection.verified_at_for_humans,
});

const cancelling = ref(false);

const submit = () => {
    form.put(route('configuration.orchestrator-connections.update', props.orchestratorConnection.id), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const verification = reactive({
    processing: false,
    recent: false,
    message: '',
    tenantsMessages: [],
});

const canVerify = computed(() => {
    // NOTE: if client_secret is touched, need to save changes before verify
    return form.hosting_type !== ''
        && (
            (
                form.hosting_type === 'cloud'
                    && form.organization_name !== ''
                    && form.client_id.trim() !== ''
                    && (!form.isDirty || form.client_secret.trim() === '')
            )
            ||
            (
                form.hosting_type == 'on_premise'
                    && form.url !== ''
                    && form.tenants.filter(tenant => {
                        return tenant.client_id && tenant.client_id.trim() !== ''
                            && (!form.isDirty || tenant.client_secret.trim() === '');
                    }).length === form.tenants.length
            )
        );
});

const updateVerification = () => {
    const message = usePage().props.value.flash.message;
    
    if (message) {
        verification.message = message.verify;
        verification.tenantsMessages = message.tenantsResults !== undefined ? message.tenantsResults : [];
    }
    props.orchestratorConnection.verified = message.extraInformation.verified;
    props.orchestratorConnection.verified_at_for_humans = message.extraInformation.verified_at_for_humans;
    if (message.extraInformation.tenantsVerificationStatuses) {
        props.orchestratorConnection.tenants.forEach(tenant => {
            const tenantInformation = message.extraInformation.tenantsVerificationStatuses.find(value => value.id === tenant.id);
            tenant.verified = tenantInformation.verified;
            tenant.verified_at_for_humans = tenantInformation.verified_at_for_humans;
        });
    }

    verification.recent = true;
    setTimeout(() => {
        verification.recent = false;
    }, 10000);
};

const verify = () => {
    verification.processing = true;
    Inertia.post(route('configuration.orchestrator-connections.verify', props.orchestratorConnection.id), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => {
            verification.processing = false;
        },
    });
};

onUpdated(() => {
    updateVerification();
});

watch(() => props.orchestratorConnection.verified,
    (verified) => form.verified = verified,
);

watch(() => props.orchestratorConnection.verified_at_for_humans, (verifiedAtForHumans) => {
    form.verified_at_for_humans = verifiedAtForHumans;
});

watch(() => props.orchestratorConnection.tenants, (tenants) => {
    form.tenants.forEach((tenant, tIndex) => {
        tenant.id = tenants.find((updated, uIndex) => uIndex == tIndex).id;
        tenant.verified = tenants.find((updated, uIndex) => uIndex == tIndex).verified;
        tenant.verified_at_for_humans = tenants.find((updated, uIndex) => uIndex == tIndex).verified_at_for_humans;
    });
});
</script>

<script>
export default {
    computed: {
        breadcrumb() {
            return [
                { href: route('configuration.index'), text: this.__('Configuration') },
                { href: route('configuration.orchestrator-connections.index'), text: this.__("UiPath Orchestrator connections") },
                { text: this.form.name },
            ];
        },
    }
}
</script>
        
<template>
    <AppLayout :title="__('Configuration') + ' > ' + __('UiPath Orchestrator connections')">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Edit a UiPath Orchestrator connection')" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <!-- orchestrator details -->
                        <OrchestratorDetails :form="form" mode="edit" :verification="verification" :canVerify="canVerify" :verify="verify" />

                        <!-- connection details -->
                        <ConnectionDetails :form="form" mode="edit" :verification="verification" :canVerify="canVerify" :verify="verify"  />

                        <!-- elasticsearch details -->
                        <ElasticSearchDetails :form="form" />

                        <!-- kibana details -->
                        <KibanaDetails :form="form" />

                        <!-- actions -->
                        <Actions :form="form" mode="edit" :cancelling="cancelling" :verification="verification" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>