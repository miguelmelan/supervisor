<script setup>
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
</script>

<template>
    <div class="pt-2 pb-3 space-y-1">
        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
            {{ __('Dashboard' ) }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('configuration.orchestrator-connections.index')"
            :active="route().current('configuration.orchestrator-connections.*')">
            {{ __('UiPath Orchestrator connections') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('configuration.automated-processes.index')"
            :active="route().current('configuration.automated-processes.*')">
            {{ __('Automated business processes' ) }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('configuration.ai-based-alert-triggers.index')"
            :active="route().current('configuration.ai-based-alert-triggers.*')">
            {{ __('AI based alert triggers' ) }}
        </ResponsiveNavLink>
    </div>
</template>