<script setup>
import { inject } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const translate = inject('translate');

const props = defineProps({
    form: Object,
    withResolutionDetails: {
        type: Boolean,
        default: false,
    },
    withNotificationName: {
        type: Boolean,
        default: true,
    },
    withComponent: {
        type: Boolean,
        default: true,
    },
    withOrchestratorConnection: {
        type: Boolean,
        default: true,
    },
    withAutomatedProcess: {
        type: Boolean,
        default: true,
    },
    withLink: {
        type: Boolean,
        default: false,
    },
});

const openAlert = () => {
    window.open(route('alerts.edit', {
        alert: props.form.id,
    }), '_blank');
};
</script>

<template>
    <div class="w-full bg-white">
        <div class="flow-root">
            <ul role="list" class="divide-y divide-gray-200">
                <li class="flex">
                    <div class="flex items-center space-x-4 p-4" :class="{
                        'w-1/2': form.read_at,
                        'w-full': !form.read_at,
                        'bg-warning-50': form.severity === 'Warn',
                        'bg-error-50': form.severity === 'Error',
                        'bg-red-900': form.severity === 'Fatal',
                        'text-orange-900': form.severity === 'Warn',
                        'text-white': form.severity === 'Fatal' || form.severity === 'Error',
                    }">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">
                                {{ __('Severity') }}
                            </p>
                            <p class="text-sm truncate font-semibold">
                                {{ __(form.severity) }}
                            </p>
                        </div>
                    </div>
                    <div v-if="form.read_at" class="flex space-x-4 w-1/2 p-4 text-green-900">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">
                                {{ __('Read date') }}
                            </p>
                            <p class="text-sm truncate">
                                {{ form.read_at }}
                            </p>
                        </div>
                    </div>
                </li>
                <li class="flex">
                    <div class="flex space-x-4 w-1/2 p-4">
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
                                {{ form.creation_time }}
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-4 w-1/2 p-4">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ __('Locking date') }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ form.locked_at ? form.locked_at : __('Alert is unlocked') }}
                            </p>
                        </div>
                    </div>
                </li>
                <li v-if="withNotificationName || withComponent" class="flex">
                    <div v-if="withNotificationName" class="flex space-x-4 w-1/2 p-4">
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
                            <p class="text-sm text-gray-500">
                                {{ __(form.notification_name) }}
                            </p>
                        </div>
                    </div>
                    <div v-if="withComponent" class="flex space-x-4 w-1/2 p-4">
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
                                {{ __(form.component) }}
                            </p>
                        </div>
                    </div>
                </li>
                <li v-if="withOrchestratorConnection || (form.automated_process && withAutomatedProcess)" class="flex">
                    <div v-if="withOrchestratorConnection" class="flex space-x-4 p-4" :class="{
                        'w-full': !form.automated_process || !withAutomatedProcess,
                        'w-1/2': form.automated_process && withAutomatedProcess,
                    }">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 truncate">
                                <dl class="max-w-md">
                                    <div class="flex flex-col pb-3">
                                        <dt class="mb-1 font-medium">{{ __('UiPath Orchestrator') }}</dt>
                                        <dd class="text-gray-500">
                                            {{ form.tenant.orchestrator_connection.code }} -
                                            {{ form.tenant.orchestrator_connection.name }}
                                        </dd>
                                    </div>
                                    <div class="flex flex-col pt-3">
                                        <dt class="mb-1 font-medium">{{ __('Tenant') }}</dt>
                                        <dd class="text-gray-500">{{ form.tenant.name }}</dd>
                                    </div>
                                </dl>
                            </p>
                        </div>
                    </div>
                    <div v-if="form.automated_process && withAutomatedProcess" class="flex space-x-4 w-1/2 p-4">
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
                                {{ form.automated_process.code }} -
                                {{ form.automated_process.name }}
                            </p>
                        </div>
                    </div>
                </li>
                <li v-if="!form.trigger" class="flex">
                    <div class="flex space-x-4 p-4 w-full">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">
                                {{ __('Associated data') }}
                            </p>
                            <JsonPretty class="text-xs" :data="JSON.parse(form._data)"
                                :deep="0" />
                        </div>
                    </div>
                </li>
                <li v-else class="flex flex-col">
                    <div class="flex space-x-4 p-4 w-full">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium">
                                {{ __('Reason') }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ JSON.parse(form._data).reason }}
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-4 p-4 w-full">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium">
                                {{ __('Sources') }}
                            </p>
                            <a v-for="source in JSON.parse(form._data).sources.filter(s => s.url)" :href="source.url" target="_blank"
                                class="inline-flex items-center text-sm font-semibold text-blue-50 tracking-widest hover:text-blue-500 active:text-blue-900 focus:outline-none disabled:opacity-25 transition">
                                {{ source.url }}
                            </a>
                            <span v-for="(source, indexS) in JSON.parse(form._data).sources.filter(s => s.host_machine_name)" class="text-sm text-gray-500" :class="{
                                'ml-2': indexS > 0,
                            }">
                                {{ source.host_machine_name }}
                            </span>
                        </div>
                    </div>
                </li>
                <li v-if="withResolutionDetails" class="flex">
                    <div class="flex space-x-4 w-1/2 p-4">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ __('Resolution details') }}
                            </p>
                            <p v-if="form.resolution_details" class="text-sm text-gray-500">
                                {{ __(form.resolution_details) }}
                            </p>
                            <p v-else class="text-sm text-gray-500 truncate">{{ __('Empty') }}</p>
                        </div>
                    </div>
                    <div v-if="form.read_at" class="flex space-x-4 w-1/2 p-4">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M10.5 8.25h3l-3 4.5h3" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ __('False positive?') }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ __(form.false_positive ? 'Yes' : 'No') }}
                            </p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <PrimaryButton v-if="withLink" class="w-full" @click.prevent="openAlert">
        {{ __('Open') }}
        <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" />
            </svg>
        </template>
    </PrimaryButton>
</template>