<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import DashboardNavLink from './NavLink/Dashboard.vue';
import OrchestratorConnectionsNavLink from './NavLink/OrchestratorConnections.vue';
import AutomatedProcessesNavLink from './NavLink/AutomatedProcesses.vue';
import AIBasedAlertTriggersNavLink from './NavLink/AIBasedAlertTriggers.vue';
import AdvancedConfigurationNavLink from './NavLink/AdvancedConfiguration.vue';
import ResponsiveNavLinks from './Responsive/NavLinks.vue';
import ResponsiveSettings from './Responsive/Settings.vue';
import TeamsDropdown from './TeamsDropdown.vue';
import SettingsDropdown from './SettingsDropdown.vue';
import Hamburger from './Hamburger.vue';

const showingNavigationDropdown = ref(false);

const hamburgerToggled = (enabled) => {
    showingNavigationDropdown.value = !enabled;
};
</script>

<template>
    <nav class="bg-white border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('dashboard')">
                            <ApplicationMark class="block h-14 w-auto" />
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <DashboardNavLink />
                    <OrchestratorConnectionsNavLink />
                    <AutomatedProcessesNavLink />
                    <AIBasedAlertTriggersNavLink />
                    <!-- <AdvancedConfigurationNavLink /> -->
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <TeamsDropdown />
                    <SettingsDropdown />
                </div>

                <Hamburger :showingNavigationDropdown="showingNavigationDropdown" @toggled="hamburgerToggled" />
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}"
            class="sm:hidden">
            <ResponsiveNavLinks />
            <ResponsiveSettings />
        </div>
    </nav>
</template>