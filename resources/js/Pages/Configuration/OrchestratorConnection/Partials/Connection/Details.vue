<script setup>
import CloudHostingTypeControls from './CloudHostingTypeControls.vue';
import OnPremiseHostingTypeControls from './OnPremiseHostingTypeControls.vue';
import Verification from './Verification.vue';
import FormStep from '@/Components/FormStep.vue';
import SectionBorder from '@/Components/SectionBorder.vue';

const props = defineProps({
    mode: String,
    form: Object,
    verification: Object,
    canVerify: Boolean,
    verify: Function,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});
</script>

<script>
export default {
    computed: {
        onPremiseDocText() {
            return this.__('For UiPath Orchestrator standalone installation or Automation Suite: :link', {
                link: '<a href="https://docs.uipath.com/orchestrator/reference/using-oauth-for-external-apps" class="text-blue-50 hover:underline font-semibold" target="about:blank">Using OAuth for external apps</a>'
            });
        },
        cloudDocText() {
            return this.__('For Automation Cloud: :link', {
                link: '<a href="https://docs.uipath.com/automation-cloud/docs/managing-external-applications" class="text-blue-50 hover:underline font-semibold" target="about:blank">Managing external applications</a>'
            });
        },
    }
}
</script>

<template>
    <div v-if="form.hosting_type && form.tenants.length > 0">
        <SectionBorder v-if="withTopBorder" />
        <FormStep>
            <template #title>
                <span
                    v-if="form.hosting_type === 'cloud' && form.tenants.filter(tenant => tenant.name.trim() !== '').length > 0">
                    {{ __('External application connection details') }}
                </span>
                <span
                    v-if="form.hosting_type === 'on_premise' && form.tenants.filter(tenant => tenant.name.trim() !== '').length > 0">
                    {{ __('External application connection details (per tenant)') }}
                </span>
            </template>
    
            <template #description>
                <div class="text-sm">
                    <p>
                        {{ __("In order to allow supervisor to interact with UiPath Orchestrator, you'll need to register the application through UiPath Identity Server with Client credentials.") }}
                    </p>
                    <p class="mt-2">
                        {{ __('For more details please access the following pages in UiPath Orchestrator documentation:') }}
                    </p>
                    <ul class="list-disc list-inside mt-1">
                        <li v-html="onPremiseDocText"></li>
                        <li v-html="cloudDocText"></li>
                    </ul>
                </div>
            </template>
    
            <template #form>
                <!-- for cloud -->
                <CloudHostingTypeControls :mode="mode" :form="form" />

                <!-- for on premise -->
                <OnPremiseHostingTypeControls :mode="mode" :form="form" :verification="verification" />

                <!-- Verification -->
                <Verification :mode="mode" :form="form" :verification="verification" :verify="verify" :canVerify="canVerify" />
            </template>
        </FormStep>
    </div>
</template>