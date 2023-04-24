<script setup>
import { inject } from 'vue';
import DetailedProperties from './DetailedProperties.vue';
import FormStep from '@/Components/FormStep.vue';

const translate = inject('translate');

const props = defineProps({
    form: Object,
});
</script>

<template>
    <FormStep>
        <template #title>
            {{ __('Details') }}
        </template>
        <template #description>
            <div class="text-sm">
                <p>{{ __('This section provides comprehensive information about the alert that is currently selected. It includes a unique identifier, the creation date of the alert, and its severity level.Additionally, it specifies the type of the alert, whether it is related to a faulted job, queue processing error, or other type of issue.The source component of the alert is also indicated, such as Jobs, Queues, Transactions, or Triggers. Eventually, the source UiPath Orchestrator or tenant is identified, providing context for where the alert originated from.') }}</p>
            </div>
        </template>
        <template #form>
            <div class="col-span-6">
                <DetailedProperties :form="form" />
            </div>
        </template>
    </FormStep>
</template>