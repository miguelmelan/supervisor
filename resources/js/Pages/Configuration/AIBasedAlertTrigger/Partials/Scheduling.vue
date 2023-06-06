<script setup>
import FormStep from '@/Components/FormStep.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';
import { ref, inject } from 'vue';

const translate = inject('translate');

const props = defineProps({
    mode: String,
    form: Object,
    withTopBorder: {
        type: Boolean,
        default: true
    },
});

const computing = ref(false);
const errorMessage = ref('');

const compute = () => {
    props.form.processing = true;
    computing.value = true;
    Inertia.post(route('configuration.ai-based-alert-triggers.compute-scheduling'), {
        recurrence: props.form.recurrence,
    }, {
        preserveScroll: true,
        onFinish: () => {
            const message = usePage().props.value.flash.message;
            if (message && message.scheduling) {
                const crons = message.scheduling.results;
                props.form.crons = crons;
                if (crons.length === 0) {
                    errorMessage.value = translate('There is no cron expression corresponding to your description, please refine it.')
                } else {
                    errorMessage.value = '';
                }
            }
            props.form.processing = false;
            computing.value = false;
        }
    });
};
</script>
    
<template>
    <SectionBorder v-if="withTopBorder" />
    <FormStep>
        <template #title>
            {{ __('Scheduling') }}
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
            <!-- Recurrence -->
            <div class="col-span-6">
                <label for="recurrence" class="sr-only">{{ __('How often do you want to pull the trigger?') }}</label>
                <textarea v-model="form.recurrence" id="recurrence"
                    rows="3" class="w-full p-0 text-gray-900 bg-white border-0 not-resizable ring-0 focus:ring-0"
                    :placeholder="__('Describe recurrence...')" required
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"></textarea>
                <InputError :message="errorMessage" class="mt-2" />
                <SectionBorder />
                <PrimaryButton @click.prevent="compute"
                    :class="{ 'opacity-25': form.processing || form.recurrence.trim() === '' }"
                    :disabled="form.processing || form.recurrence.trim() === ''">
                    {{ __('Apply scheduling') }}
                    <template #icon>
                        <svg v-if="computing" aria-hidden="true" role="status" class="animate-spin"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="#E5E7EB" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentColor" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </PrimaryButton>
                <div v-if="form.crons.length > 0 && !computing">
                    <dl class="text-gray-900 divide-y divide-gray-200 mt-3">
                        <div v-for="(cron, index) in form.crons" class="flex flex-col" :class="{ 'pb-3': index !== form.crons.length - 1}">
                            <dt class="flex items-center mb-1 text-gray-500 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ cron.description }}</span>
                            </dt>
                            <dd class="text-xs font-semibold">{{ cron.cron }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </template>
    </FormStep>
</template>