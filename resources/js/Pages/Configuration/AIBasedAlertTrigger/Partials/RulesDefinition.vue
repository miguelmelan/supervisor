<script setup>
import { ref, inject } from 'vue';
import FormStep from '@/Components/FormStep.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';

const translate = inject('translate');

const props = defineProps({
    mode: String,
    form: Object,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});

const checking = ref(false);
const errorMessage = ref('');

const check = () => {
    props.form.processing = true;
    checking.value = true;
    Inertia.post(route('configuration.ai-based-alert-triggers.check-conditions'), {
        conditions: props.form.conditions,
        orchestrator_connections: props.form.orchestrator_connections,
    }, {
        preserveScroll: true,
        onFinish: () => {
            const message = usePage().props.value.flash.message;
            if (message && message.verifications) {
                const results = message.verifications;
                props.form.verifications = results;
            }
            props.form.processing = false;
            checking.value = false;
        }
    });
};
</script>
    
<template>
    <SectionBorder v-if="withTopBorder" />
    <FormStep>
        <template #title>
            {{ __('Rules definition') }}
        </template>

        <template #description>
            <div class="text-sm">
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Non beatae, aspernatur incidunt, hic vel ea
                    modi reiciendis cumque eos itaque numquam suscipit exercitationem earum voluptates illo. Vero
                    laborum illo id.
                </p>
            </div>
        </template>

        <template #form>
            <div class="col-span-6">
                <!-- <MultiTextInput id="conditions" v-model="form.conditions" _key="text"
                    :_required="true" :model="conditionModel"
                    :buttonsDisabled="form.processing"
                    :fieldsDisabled="form.processing" /> -->
                <textarea v-model="form.conditions" id="conditions"
                    rows="5" class="w-full p-0 text-gray-900 bg-white border-0 not-resizable ring-0 focus:ring-0"
                    :placeholder="__('Describe conditions...')" required
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"></textarea>
                <!-- <div class="mt-3" v-if="form.classifications.length > 0">
                    <span v-for="classification in form.classifications" class="bg-indigo-100 text-indigo-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">{{ __(classification.data_source) }}</span>
                </div> -->
                <InputError :message="errorMessage" class="mt-2" />
                <SectionBorder />
                <PrimaryButton @click.prevent="check" :class="{ 'opacity-25': form.processing || form.conditions.trim() === '' }"
                    :disabled="form.processing || form.conditions.trim() === ''">
                    <template #icon>
                        <svg v-if="checking" aria-hidden="true" role="status" class="animate-spin"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="#E5E7EB" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentColor" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </template>
                    {{ __('Check conditions') }}
                </PrimaryButton>
                <div v-if="form.verifications.length > 0 && !checking" v-for="(verification, indexV) in form.verifications" class="mt-2">
                    <p class="text-sm text-gray-900 truncate">
                        <dl v-for="(tenant, indexT) in verification.orchestrator_connection.tenants">
                            <SectionBorder v-if="indexT > 0" height="md" />
                            <div class="flex items-center" :class="{ 'pt-3': indexT === 0 }">
                                <div class="flex flex-col w-1/2">
                                    <dt class="mb-1 font-bold">{{ __('UiPath Orchestrator') }}</dt>
                                    <dd class="text-gray-500">
                                        {{ verification.orchestrator_connection.code }} -
                                        {{ verification.orchestrator_connection.name }}
                                    </dd>
                                </div>
                                <div class="flex flex-col w-1/2">
                                    <dt class="mb-1 font-bold">{{ __('Tenant') }}</dt>
                                    <dd class="text-gray-500">{{ tenant.name }}</dd>
                                </div>
                            </div>
                            <SectionBorder />
                            <dl class="text-gray-900 divide-y divide-gray-200">
                                <div v-for="(tenantVerification, index) in verification.tenants[tenant.id].verifications" class="flex flex-col" :class="{
                                        'pb-3': index !== verification.tenants[tenant.id].verifications.length - 1,
                                        'pt-3': index > 0,
                                    }">
                                    <dt class="flex items-center mb-1 text-gray-500 text-sm">
                                        <svg v-if="tenantVerification.answer.result" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-error-50 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" />
                                        </svg>
                                        <svg v-else-if="tenantVerification.answer.error" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-error-50 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-success-50 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.143 17.082a24.248 24.248 0 003.844.148m-3.844-.148a23.856 23.856 0 01-5.455-1.31 8.964 8.964 0 002.3-5.542m3.155 6.852a3 3 0 005.667 1.97m1.965-2.277L21 21m-4.225-4.225a23.81 23.81 0 003.536-1.003A8.967 8.967 0 0118 9.75V9A6 6 0 006.53 6.53m10.245 10.245L6.53 6.53M3 3l3.53 3.53" />
                                        </svg>
                                        <span v-if="tenantVerification.answer.reason">{{ tenantVerification.answer.reason }}</span>
                                        <span v-if="tenantVerification.answer.error">{{ tenantVerification.answer.error }}</span>
                                    </dt>
                                    <dd v-for="text in tenantVerification.related_text_parts" class="text-xs font-semibold">{{ text }}</dd>
                                    <div v-if="tenantVerification.data_source !== 'Machines details'" class="flex mt-2">
                                        <a v-for="(source, indexS) in tenantVerification.answer.sources" :href="source" target="_blank"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-50 border border-transparent rounded-md font-semibold text-white tracking-widest hover:bg-blue-500 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition" :class="{
                                            'ml-2': indexS > 0,
                                        }">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                            </svg>
                                            <span v-if="tenantVerification.data_source === 'Jobs execution history' || tenantVerification.data_source === 'Robots logs'">
                                                {{ __('Job n°' + (indexS + 1)) }}
                                            </span>
                                            <span v-if="tenantVerification.data_source === 'Machines details'">
                                                {{ __('Machine n°' + (indexS + 1)) }}
                                            </span>
                                            <span v-if="tenantVerification.data_source === 'Queue items details'">
                                                {{ __('Queue n°' + (indexS + 1)) }}
                                            </span>
                                        </a>
                                    </div>
                                    <div v-else class="flex mt-2">
                                        <span v-for="source in tenantVerification.answer.sources">
                                            {{ source }}
                                        </span>
                                    </div>
                                </div>
                            </dl>
                        </dl>
                    </p>
                </div>
            </div>
        </template>
    </FormStep>
</template>