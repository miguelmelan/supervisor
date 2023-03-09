<script setup>
import FormStep from '@/Components/FormStep.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    form: Object,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});
</script>
    
<script>
export default {
    computed: {
        kibanaDocText() {
            return this.__('For more details please access the following page in UiPath Orchestrator documentation: :link', {
                link: '<a href="https://docs.uipath.com/installation-and-upgrade/docs/creating-an-index-pattern-to-connect-to-elasticsearch" class="text-blue-50 hover:underline font-semibold" target="about:blank">Creating an index pattern to connect to Elasticsearch</a>.'
            });
        },
    }
}
</script>

<template>
    <div v-if="form.hosting_type === 'on_premise' && form.kibana_enabled">
        <SectionBorder v-if="withTopBorder" />
        <FormStep>
            <template #title>
                {{ __('Kibana details') }}
            </template>

            <template #description>
                <div class="text-sm">
                    <p>
                        {{ __('Kibana retrieves the logs from Elasticsearch and lets you visualize and analyze the data.') }}
                    </p>
                    <p class="mt-2">
                        {{ __('If Kibana is configured in your installation you may provide its URL allowing supervisor to generate direct links to some Kibana pages.') }}
                    </p>
                    <p class="mt-2" v-html="kibanaDocText"></p>
                </div>
            </template>

            <template #form>
                <!-- URL -->
                <div class="col-span-6">
                    <InputLabel for="kibana-url" value="URL" />
                    <TextInput id="kibana-url" v-model="form.kibana_url" type="url"
                        class="mt-1 block w-full" autocomplete="kibana-url" required />
                    <InputError :message="form.errors.kibana_url" class="mt-2" />
                </div>
            </template>
        </FormStep>
    </div>
</template>