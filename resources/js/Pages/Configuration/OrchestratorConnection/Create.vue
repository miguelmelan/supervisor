<script setup>
import { computed, onUpdated, reactive, ref, inject } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Actions from './Partials/Actions.vue';
import ConnectionDetails from './Partials/Connection/Details.vue';
import ElasticSearchDetails from './Partials/ElasticSearchDetails.vue';
import KibanaDetails from './Partials/KibanaDetails.vue';
import OrchestratorDetails from './Partials/OrchestratorDetails.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';

const translate = inject('translate');

const form = useForm({
    code: '',
    name: '',
    hosting_type: '',
    url: '',
    organization_name: '',
    tenants: [{
        id: 0,
        name: 'Default',
        client_id: '',
        client_secret: '',
        verified: null,
        verified_at_for_humans: null,
    }],
    environment_type: '',
    client_id: '',
    client_secret: '',
    elasticsearch_enabled: false,
    elasticsearch_index_configuration: '',
    elasticsearch_url: '',
    elasticsearch_anonymous_authentication: false,
    elasticsearch_username: '',
    elasticsearch_password: '',
    kibana_enabled: false,
    kibana_url: '',
    tags: [],
});

const cancelling = ref(false);

const submit = () => {
    form.post(route('configuration.orchestrator-connections.store'), {
        replace: true
    });
};

const verification = reactive({
    processing: false,
    recent: false,
    message: '',
    tenantsMessages: [],
});

const breadcrumb = computed(() => {
    return [
        { href: route('configuration.index'), text: translate('Configuration') },
        { href: route('configuration.orchestrator-connections.index'), text: translate("UiPath Orchestrator connections") },
        { text: translate('Create') },
    ];
});

const canVerify = computed(() => {
    return form.hosting_type !== ''
        && (
            (form.hosting_type === 'cloud'
                && form.organization_name !== ''
                && form.client_id.trim() !== ''
                && form.client_secret.trim() !== '')
            ||
            (form.hosting_type == 'on_premise'
                && form.url !== ''
                && form.tenants.filter(tenant => {
                    return tenant.client_id.trim() !== ''
                        && tenant.client_secret.trim() !== '';
                }).length === form.tenants.length)
        );
});

const updateVerification = () => {
    const message = usePage().props.value.flash.message;

    if (message) {
        verification.message = message.verify;
        verification.tenantsMessages = message.tenantsResults !== undefined ? message.tenantsResults : [];
        form.verified = message.extraInformation.verified;
        form.verified_at_for_humans = message.extraInformation.verified_at_for_humans;
        if (message.extraInformation.tenantsVerificationStatuses) {
            form.tenants.forEach(tenant => {
                const tenantInformation = message.extraInformation.tenantsVerificationStatuses.find(value => value.id === tenant.id);
                tenant.verified = tenantInformation.verified;
                tenant.verified_at_for_humans = tenantInformation.verified_at_for_humans;
            });
        }
    }

    verification.recent = true;
    setTimeout(() => {
        verification.recent = false;
    }, 10000);
};

const verify = () => {
    verification.processing = true;
    let payload = {
        hosting_type: form.hosting_type,
    };
    if (form.hosting_type === 'cloud') {
        payload.client_id = form.client_id;
        payload.client_secret = form.client_secret;
    } else if (form.hosting_type === 'on_premise') {
        payload.url = form.url;
        payload.tenants = form.tenants;
    }
    Inertia.post(route('configuration.orchestrator-connections.verify'), payload, {
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
</script>

<template>
    <AppLayout :title="__('Configuration') + ' > ' + __('UiPath Orchestrator connections')">
        <template #header>
            <Breadcrumb :items="breadcrumb" />
        </template>
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Connect with a UiPath Orchestrator')" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <!-- orchestrator details -->
                        <OrchestratorDetails :form="form" mode="create" :verification="verification" :canVerify="canVerify" :verify="verify" />

                        <!-- connection details -->
                        <ConnectionDetails :form="form" mode="create" :verification="verification" :canVerify="canVerify" :verify="verify"  />

                        <!-- elasticsearch details -->
                        <ElasticSearchDetails :form="form" />

                        <!-- kibana details -->
                        <KibanaDetails :form="form" />

                        <!-- actions -->
                        <Actions :form="form" mode="create" :cancelling="cancelling" :verification="verification" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>