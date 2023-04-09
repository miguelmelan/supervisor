<script setup>
import { inject } from 'vue';
import FormStep from '@/Components/FormStep.vue';

const translate = inject('translate');

const props = defineProps({
    alert: Object,
    form: Object,
});
</script>

<template>
    <FormStep>
        <template #title>
            {{ __('Extensions') }}
        </template>
        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('This section provides users with the ability to connect their alerts to other systems, such as Jira, ServiceNow, Slack, or any other system that supports API-based integration. This section serves as a bridge between the alerting system and external systems, enabling users to report and share information about alerts with other teams and stakeholders.') }}
                </p>
            </div>
        </template>
        <template #form>
            <div class="col-span-6">
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-red-500 text-white rounded-md h-24 flex items-center justify-center">ServiceNow</div>
                    <div class="bg-indigo-500 text-white rounded-md h-24 flex items-center justify-center">Atlassian Jira</div>
                    <div class="bg-cyan-500 text-white rounded-md h-24 flex items-center justify-center">Slack</div>
                </div>
            </div>
        </template>
    </FormStep>
</template>