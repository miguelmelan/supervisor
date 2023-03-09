<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    mode: String,
    form: Object,
});
</script>

<template>
    <!-- Client ID -->
    <div v-if="form.hosting_type === 'cloud'" class="col-span-12">
        <InputLabel for="client-id" :value="__('App ID')" />
        <TextInput id="client-id" v-model="form.client_id" type="text" class="mt-1 block w-full"
            autocomplete="client-id" required />
        <InputError :message="form.errors.client_id" class="mt-2" />
    </div>
    <!-- Client Secret -->
    <div v-if="form.hosting_type === 'cloud'" class="col-span-12">
        <InputLabel for="client-secret" :value="__('App Secret')" />
        <TextInput v-if="mode == 'create'" id="client-secret" v-model="form.client_secret" type="password"
            class="mt-1 block w-full" autocomplete="client-secret" required />
        <TextInput v-else id="client-secret" v-model="form.client_secret" type="password" class="mt-1 block w-full"
            autocomplete="client-secret" />
        <InputError :message="form.errors.client_secret" class="mt-2" />
    </div>
</template>