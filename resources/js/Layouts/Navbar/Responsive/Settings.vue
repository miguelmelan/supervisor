<script setup>
import { Inertia } from '@inertiajs/inertia';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const switchToTeam = (team) => {
    Inertia.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    Inertia.post(route('logout'));
};
</script>

<template>
    <div class="pt-4 pb-1 border-t border-gray-200">
        <div class="flex items-center px-4">
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                <img class="h-10 w-10 rounded-full object-cover"
                    :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
            </div>

            <div>
                <div class="font-medium text-base text-gray-800">
                    {{ $page.props.user.name }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                    {{ $page.props.user.email }}
                </div>
            </div>
        </div>

        <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                {{ __('Profile' ) }}
            </ResponsiveNavLink>

            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                {{ __('API Tokens' ) }}
            </ResponsiveNavLink>

            <!-- Authentication -->
            <form method="POST" @submit.prevent="logout">
                <ResponsiveNavLink as="button">
                    {{ __('Log Out' ) }}
                </ResponsiveNavLink>
            </form>

            <!-- Team Management -->
            <template v-if="$page.props.jetstream.hasTeamFeatures">
                <div class="border-t border-gray-200" />

                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Manage Team' ) }}
                </div>

                <!-- Team Settings -->
                <ResponsiveNavLink :href="route('teams.show', $page.props.user.current_team)"
                    :active="route().current('teams.show')">
                    {{ __('Team Settings' ) }}
                </ResponsiveNavLink>

                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                    :href="route('teams.create')" :active="route().current('teams.create')">
                    {{ __('Create New Team' ) }}
                </ResponsiveNavLink>

                <div class="border-t border-gray-200" />

                <!-- Team Switcher -->
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Switch Teams' ) }}
                </div>

                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                    <form @submit.prevent="switchToTeam(team)">
                        <ResponsiveNavLink as="button">
                            <div class="flex items-center">
                                <svg v-if="team.id == $page.props.user.current_team_id"
                                    class="mr-2 h-5 w-5 text-green-400" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>{{ team.name }}</div>
                            </div>
                        </ResponsiveNavLink>
                    </form>
                </template>
            </template>
        </div>
    </div>
</template>