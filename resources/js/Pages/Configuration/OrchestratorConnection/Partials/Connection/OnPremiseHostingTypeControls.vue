<script setup>
import TenantTab from './TenantTab.vue';
import TenantTabContent from './TenantTabContent.vue';

const props = defineProps({
    mode: String,
    form: Object,
    verification: Object,
});
</script>

<template>
    <div v-if="form.hosting_type === 'on_premise'" class="col-span-12">
        <ul class="flex flex-wrap -mb-px" id="tenants-connection-tab"
            data-tabs-toggle="#tenants-connection-tab-content" role="tablist">
            <li v-for="(tenant, index) in form.tenants.filter(tenant => tenant.name)" class="mr-2">
                <TenantTab :id="index.toString()" :tenant="tenant" />
            </li>
        </ul>
        <div id="tenants-connection-tab-content" class="mt-3">
            <div v-for="(tenant, index) in form.tenants.filter(tenant => tenant.name)"
                class="hidden p-4 bg-gray-50 rounded-lg" :id="'tenant-connection-' + index" role="tabpanel"
                :aria-labelledby="'tenant-' + index + '-tab'" :key="index">
                <TenantTabContent :mode="mode" :form="form" :verification="verification" :id="index.toString()" :tenant="tenant" />
            </div>
        </div>
    </div>
</template>