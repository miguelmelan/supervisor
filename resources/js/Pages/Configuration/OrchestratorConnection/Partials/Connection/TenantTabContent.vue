<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import VerificationStatus from '@/Components/VerificationStatus.vue';

const props = defineProps({
    mode: String,
    form: Object,
    verification: Object,
    id: String,
    tenant: Object,
});
</script>

<template>
    <!-- Verification -->
    <div>
        <InputLabel :value="__('Verification status')" />
        <VerificationStatus class="mt-1" :verified="tenant.verified" :verified-at-for-humans="tenant.verified_at_for_humans"  />
    </div>
    <!-- Client ID -->
    <div class="mt-6">
        <InputLabel :for="`tenant-${id}-client-id`" :value="__('App ID')" />
        <TextInput :id="`tenant-${id}-client-id`" v-model="tenant.client_id" type="text" class="mt-1 block w-full"
            :autocomplete="`tenant-${id}-client-id`" required />
        <!-- <InputError :message="form.errors.client_id" class="mt-2" /> -->
    </div>
    <!-- Client Secret -->
    <div class="mt-6">
        <InputLabel :for="`tenant-${id}-client-secret`" :value="__('App Secret')" />
        <TextInput v-if="mode == 'create'" :id="`tenant-${id}-client-secret`" v-model="tenant.client_secret"
            type="password" class="mt-1 block w-full" :autocomplete="`tenant-${id}-client-secret`" required />
        <TextInput v-else :id="`tenant-${id}-client-secret`" v-model="tenant.client_secret" type="password"
            class="mt-1 block w-full" :autocomplete="`tenant-${id}-client-secret`" />
        <!-- <InputError :message="form.errors.client_secret" class="mt-2" /> -->
    </div>
    <InputError
        :message="verification.tenantsMessages && verification.tenantsMessages[id] ? verification.tenantsMessages[id].error : null"
        class="mt-2" />
</template>