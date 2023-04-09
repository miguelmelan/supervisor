<script setup>
import { inject } from 'vue';
import FormStep from '@/Components/FormStep.vue';

const translate = inject('translate');

const props = defineProps({
    alert: Object,
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
                <p>{{ __('This section provides comprehensive information about the alert that is currently selected. It includes a unique identifier, the creation date of the alert, and its severity level.Additionally, it specifies the type of the alert, whether it is related to a faulted job, queue processing error, or other type of issue.The source component of the alert is also indicated, such as Jobs, Queues, Transactions, or Triggers.Finally, the source UiPath Orchestrator or tenant is identified, providing context for where the alert originated from. ') }}</p>
            </div>
        </template>
        <template #form>
            <div class="col-span-6">
                <div class="w-full bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-xl font-bold leading-none text-gray-900">{{ '#' + form.id }}</h5>
                    </div>
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200">
                            <li class="flex">
                                <div class="flex items-center space-x-4 w-1/2 p-4">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ __('Creation date') }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ alert.creation_time }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 w-1/2 p-4" :class="{
                                    'bg-warning-50': alert.severity === 'Warn',
                                    'bg-error-50': alert.severity === 'Error',
                                    'bg-red-900': alert.severity === 'Fatal',
                                }">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8" :class="{
                                                'text-orange-900': alert.severity === 'Warn',
                                                'text-white': alert.severity === 'Fatal' || alert.severity === 'Error',
                                            }">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate" :class="{
                                            'text-orange-900': alert.severity === 'Warn',
                                            'text-white': alert.severity === 'Fatal' || alert.severity === 'Error',
                                        }">
                                            {{ __('Severity') }}
                                        </p>
                                        <p class="text-sm truncate font-semibold" :class="{
                                            'text-orange-900': alert.severity === 'Warn',
                                            'text-white': alert.severity === 'Fatal' || alert.severity === 'Error',
                                        }">
                                            {{ __(alert.severity) }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="flex">
                                <div class="flex items-center space-x-4 w-1/2 p-4">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ __('Type') }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ __(alert.notification_name) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 w-1/2 p-4">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ __('Component') }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ __(alert.component) }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li class="flex">
                                <div class="flex items-center space-x-4 w-1/2 p-4">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-500 truncate">
                                        <dl class="max-w-md text-gray-900">
                                            <div class="flex flex-col pb-3">
                                                <dt class="mb-1 text-gray-500">{{ __('UiPath Orchestrator') }}</dt>
                                                <dd class="font-semibold">
                                                    {{ alert.tenant.orchestrator_connection.code }} -
                                                    {{ alert.tenant.orchestrator_connection.name }}
                                                </dd>
                                            </div>
                                            <div class="flex flex-col pt-3">
                                                <dt class="mb-1 text-gray-500">{{ __('Tenant') }}</dt>
                                                <dd class="font-semibold">{{ alert.tenant.name }}</dd>
                                            </div>
                                        </dl>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 w-1/2 p-4">
                                    <div class="flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ __('Automated business process') }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            <span v-if="alert.automated_process">
                                                {{ alert.automated_process.code }} -
                                                {{ alert.automated_process.name }}
                                            </span>
                                            <span v-else>{{ ('None') }}</span>
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            </div>
        </template>
    </FormStep>
</template>