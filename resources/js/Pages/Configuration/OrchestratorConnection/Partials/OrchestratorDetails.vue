<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { debounce } from 'lodash';
import 'flowbite';
import EnvironmentTypeControls from './EnvironmentTypeControls.vue';
import FormStep from '@/Components/FormStep.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import MultiTextInput from '@/Components/MultiTextInput.vue';
import TagsInput from '@/Components/TagsInput.vue';
import TextInput from '@/Components/TextInput.vue';
import Toggle from '@/Components/Toggle.vue';
import HostingTypeControls from './HostingTypeControls.vue';

const props = defineProps({
    mode: String,
    form: Object,
    verification: Object,
    canVerify: Boolean,
    verify: Function,
});

const name = computed(() => props.form.name);
const hostingType = computed(() => props.form.hosting_type);
const codeTouched = ref(false);
const tabs = ref(null);

const tenantModel = computed(() => {
    return {
        id: null,
        name: '',
        client_id: '',
        client_secret: '',
        verified: null,
        verified_at_for_humans: null,
    };
});

watch(name, debounce(function (value) {
    if (props.mode == 'create' && !codeTouched.value) {
        axios.post(route('configuration.generate-code'), {
            name: value,
        }).then(res => {
            props.form.code = res.data.code;
        });
    };
}, 500));

watch(hostingType, value => {
    triggerTenantsConnectionTabManger();
});

const triggerTenantsConnectionTabManger = () => {
    if (props.form.hosting_type === 'on_premise') {
        manageTenantsConnectionTab();
    }
};

const manageTenantsConnectionTab = () => {
    setTimeout(() => {
        let firstId = null;
        const tabElements = props.form.tenants.filter(tenant => tenant.name).map((tenant, index) => {
            if (props.mode == 'create') {
                tenant.id = index;
            }

            if (firstId == null) {
                firstId = index;
            }
            const triggerEl = document.querySelector(`#tenant-${index}-tab`);
            const targetEl = document.querySelector(`#tenant-connection-${index}`);
            if (triggerEl && targetEl) {
                return {
                    id: `${index}`,
                    triggerEl: triggerEl,
                    targetEl: targetEl,
                };
            }
        }).filter(element => element != undefined);

        // options with default values
        const options = {
            defaultTabId: `#tenant-${firstId}`,
            activeClasses: 'text-blue-600 hover:text-blue-600 border-blue-600',
            inactiveClasses: 'text-gray-500 hover:text-gray-600 border-gray-100 hover:border-gray-300',
            onShow: () => {}
        };

        if (tabElements) {
            let activeTab = null;
            if (tabs.value) {
                activeTab = tabs.value.getActiveTab();
            }
            tabs.value = new Tabs(tabElements, options);
            if (activeTab) {
                try {
                    tabs.value.show(activeTab.id);
                } catch (e) {
                    // tab was removed
                }
            }
        }
    }, 250);
};

onMounted(() => {
    triggerTenantsConnectionTabManger();
});
</script>

<template>
    <FormStep>
        <template #title>
            {{ __('UiPath Orchestrator details') }}
        </template>

        <template #description>
            <div class="text-sm">
                <p>
                    {{ __('You can create connections with Cloud (Automation Cloud) and On-Premise (standalone or Automation Suite) hosted UiPath Orchestrator instances.') }}
                </p>
            </div>
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" :value="__('Name')" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" autocomplete="name" required />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Code -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="code" :value="__('Code')" />
                <TextInput id="code" v-model="form.code" type="text" class="mt-1 block w-full"
                    autocomplete="code" required minlength="2" maxlength="5" @keypress="codeTouched = (form.code != '')" />
                <InputError :message="form.errors.code" class="mt-2" />
            </div>

            <!-- Tags -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="tags" :value="__('Tags')" />
                <TagsInput v-if="mode == 'create'" v-model="form.tags" />
                <TagsInput v-if="mode == 'edit'" v-model="form.tags" />
                <InputError :message="form.errors.tags" class="mt-2" />
            </div>

            <!-- Environment Type -->
            <EnvironmentTypeControls :form="form" />

            <!-- Hosting Type -->
            <HostingTypeControls :form="form" :verification="verification" />

            <!-- URL -->
            <div v-if="form.hosting_type === 'on_premise'" class="col-span-6">
                <InputLabel for="url" :value="__('URL')" />
                <TextInput id="url" v-model="form.url" type="url" class="mt-1 block w-full"
                    autocomplete="url" required />
                <InputError :message="form.errors.url" class="mt-2" />
            </div>

            <!-- Organization Name -->
            <div v-if="form.hosting_type === 'cloud'" class="col-span-6 sm:col-span-4">
                <InputLabel for="organization-name" :value="__('Organization Name')" />
                <TextInput id="organization-name" v-model="form.organization_name" type="text"
                    class="mt-1 block w-full" autocomplete="organization-name" required />
                <InputError :message="form.errors.organization_name" class="mt-2" />
            </div>

            <!-- Tenants -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="tenants" :value="__('Tenants')" />
                <MultiTextInput id="tenants" v-model="form.tenants" _key="name" :_required="true" :model="tenantModel" @update:modelValue="manageTenantsConnectionTab" :buttonsDisabled="form.processing || verification.processing" />
                <InputError :message="form.errors.tenants" class="mt-2" />
            </div>

            <!-- Other options -->
            <div v-if="form.hosting_type === 'on_premise'" class="col-span-6 sm:col-span-4 grid grid-cols-12 gap-6">
                <h3 class="text-md font-semibold text-gray-900 col-span-12">
                    {{ __('Other options') }}
                </h3>
                <!-- Elasticsearch enabled -->
                <div v-if="form.hosting_type === 'on_premise'" class="col-span-12">
                    <Toggle id="elasticsearch_enabled" v-model:checked="form.elasticsearch_enabled" :label="__('Elasticsearch enabled')" />
                </div>
                <!-- Kibana enabled -->
                <div v-if="form.hosting_type === 'on_premise'" class="col-span-12">
                    <Toggle id="kibana_enabled" v-model:checked="form.kibana_enabled" :label="__('Kibana enabled')" />
                </div>
            </div>
        </template>
    </FormStep>
</template>