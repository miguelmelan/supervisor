<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormStep from '@/Components/FormStep.vue';
import PageContentHeader from '@/Components/PageContentHeader.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import Actions from './Partials/Actions.vue';

const translate = inject('translate');

const props = defineProps({
    alert: Object,
});

let form = useForm({
    id: props.alert.id,
});

</script>

<template>
    <AppLayout :title="__('Pending alerts') + ' > ' + __('Alert') + ` #${alert.id}`">        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg">
                <PageContentHeader :text="__('Edit alert #:id', {
                    id: alert.id,
                })" />

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                    <form @submit.prevent="submit">
                        <FormStep>
                            <template #title>
                                {{ __('Details') }}
                            </template>
                            <template #description>
                                <div class="text-sm">
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt magni exercitationem, consequatur quia rerum quo iure. Architecto molestiae perferendis, debitis, nihil sequi molestias animi corrupti modi incidunt quas impedit quam?</p>
                                </div>
                            </template>
                            <template #form>
                                <div class="col-span-6 sm:col-span-4">Alert details ...</div>
                            </template>
                        </FormStep>

                        <SectionBorder />
                        <FormStep>
                            <template #title>
                                {{ __('Resolution details') }}
                            </template>
                            <template #description>
                                <div class="text-sm">
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt magni exercitationem, consequatur quia rerum quo iure. Architecto molestiae perferendis, debitis, nihil sequi molestias animi corrupti modi incidunt quas impedit quam?</p>
                                </div>
                            </template>
                            <template #form>
                                <div class="col-span-6 sm:col-span-4">Resolution details ...</div>
                            </template>
                        </FormStep>

                        <SectionBorder />
                        <FormStep>
                            <template #title>
                                {{ __('Extensions') }}
                            </template>
                            <template #description>
                                <div class="text-sm">
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt magni exercitationem, consequatur quia rerum quo iure. Architecto molestiae perferendis, debitis, nihil sequi molestias animi corrupti modi incidunt quas impedit quam?</p>
                                </div>
                            </template>
                            <template #form>
                                <div class="col-span-6 sm:col-span-4">Extensions ...</div>
                            </template>
                        </FormStep>

                        <SectionBorder />
                        <FormStep>
                            <template #title>
                                {{ __('Timeline') }}
                            </template>
                            <template #description>
                                <div class="text-sm">
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt magni exercitationem, consequatur quia rerum quo iure. Architecto molestiae perferendis, debitis, nihil sequi molestias animi corrupti modi incidunt quas impedit quam?</p>
                                </div>
                            </template>
                            <template #form>
                                <div class="col-span-6 sm:col-span-4">Timeline ...</div>
                            </template>
                        </FormStep>

                        <!-- actions -->
                        <Actions :form="form" mode="edit" />
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>