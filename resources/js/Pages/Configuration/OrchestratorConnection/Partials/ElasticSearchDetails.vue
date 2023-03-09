<script setup>
import FormStep from '@/Components/FormStep.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TextInput from '@/Components/TextInput.vue';
import Toggle from '@/Components/Toggle.vue';

const props = defineProps({
    form: Object,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});

const manageCredentials = () => {
    if (props.form.elasticsearch_anonymous_authentication) {
        props.form.elasticsearch_username = '';
        props.form.elasticsearch_password = '';
    }
};
</script>
    
<script>
export default {
    computed: {
        elasticsearchDocText() {
            return this.__('For more details please access the following page in UiPath Orchestrator documentation: :link', {
                link: '<a href="https://docs.uipath.com/installation-and-upgrade/docs/uipath-orchestrator-dll-config#elasticsearch" class="text-blue-50 hover:underline font-semibold" target="about:blank">UiPath Orchestrator configuration > Elasticsearch</a>.'
            });
        },
    }
}
</script>

<template>
    <div v-if="form.hosting_type === 'on_premise' && form.elasticsearch_enabled">
        <SectionBorder v-if="withTopBorder" />
        <FormStep>
            <template #title>
                {{ __('Elasticsearch details') }}
            </template>

            <template #description>
                <div class="text-sm">
                    <p>
                        {{ __('If your UiPath Orchestrator sends logs to Elasticsearch, you can specify some parameters allowing supervisor to make requests on stored data.') }}
                    </p>
                    <p class="mt-2">
                        {{ __("In order to allow supervisor to interact with UiPath Orchestrator, you'll need to register the application through UiPath Identity Server with Client credentials.") }}
                    </p>
                    <p class="mt-2" v-html="elasticsearchDocText"></p>
                </div>
            </template>

            <template #form>
                <!-- URL -->
                <div class="col-span-6">
                    <InputLabel for="elasticsearch-url" :value="__('URL')" />
                    <TextInput id="elasticsearch-url" v-model="form.elasticsearch_url"
                        type="url" class="mt-1 block w-full" autocomplete="elasticsearch-url"
                        required />
                    <InputError :message="form.errors.elasticsearch_url" class="mt-2" />
                </div>
                <!-- Anonymous -->
                <div class="col-span-6 sm:col-span-4">
                    <Toggle id="elasticsearch_anonymous_authentication"
                        v-model:checked="form.elasticsearch_anonymous_authentication"
                        :label="__('Anonymous authentication')"
                        @change="manageCredentials" />
                </div>
                <!-- Username -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="elasticsearch-username" :value="__('Username')" />
                    <TextInput id="elasticsearch-username" v-model="form.elasticsearch_username"
                        type="text" class="mt-1 block w-full"
                        autocomplete="elasticsearch-username"
                        :inactive="form.elasticsearch_anonymous_authentication"
                        :disabled="form.elasticsearch_anonymous_authentication" />
                    <InputError :message="form.errors.elasticsearch_username" class="mt-2" />
                </div>
                <!-- Password -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="elasticsearch-password" :value="__('Password')" />
                    <TextInput id="elasticsearch-password" v-model="form.elasticsearch_password"
                        type="password" class="mt-1 block w-full"
                        autocomplete="elasticsearch-password"
                        :inactive="form.elasticsearch_anonymous_authentication"
                        :disabled="form.elasticsearch_anonymous_authentication" />
                    <InputError :message="form.errors.elasticsearch_password" class="mt-2" />
                </div>
                <!-- Index Configuration -->
                <div class="col-span-6">
                    <InputLabel for="elasticsearch-index-configuration"
                        :value="__('Index Configuration')" />
                    <TextInput id="elasticsearch-index-configuration"
                        v-model="form.elasticsearch_index_configuration" type="text"
                        class="mt-1 block w-full"
                        autocomplete="elasticsearch-index-configuration" required />
                    <InputError :message="form.errors.elasticsearch_index_configuration"
                        class="mt-2" />
                </div>
            </template>
        </FormStep>
    </div>
</template>