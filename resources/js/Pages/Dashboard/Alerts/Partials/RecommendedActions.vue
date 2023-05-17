<script setup>
import FormStep from '@/Components/FormStep.vue';
</script>

<template>
    <FormStep>
        <template #title>
            {{ __('Recommended actions') }}
        </template>
        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('This section contains information about how the user resolved the alert. This includes a description of the actions taken to address the issue, any changes made to the system or process, and any additional notes or comments related to the resolution. The section serves as a record of the steps taken to resolve the alert, providing valuable information for future reference.') }}
                </p>
            </div>
        </template>
        <template #form>
            <div class="col-span-6">...</div>
        </template>
    </FormStep>
</template>