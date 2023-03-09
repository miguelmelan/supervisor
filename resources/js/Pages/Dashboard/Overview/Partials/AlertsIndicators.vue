<script setup>
import { ref, inject, computed } from 'vue';
import AlertsIndicator from '../Charts/AlertsIndicator.vue';

const translate = inject('translate');

const props = defineProps({
    pendingAlertsCount: Number,
    alertsEveryFifteenMinutes: Object,
    alertsEveryFifteenMinutesCategories: Array,
    alertsEveryHour: Object,
    alertsEveryHourCategories: Array,
    alertsEveryFourHours: Object,
    alertsEveryFourHoursCategories: Array,
    alertsEveryday: Object,
    alertsEverydayCategories: Array,
    alertsEveryWeek: Object,
    alertsEveryWeekCategories: Array,
    alertsEveryMonth: Object,
    alertsEveryMonthCategories: Array,
});

const backgroundColors = {
    everyFifteenMinutes: '#a78bfa',
    everyHour: '#38c6f4',
    everyFourHours: '#f472b6',
    everyday: '#fb7185',
    everyWeek: '#818cf8',
    everyMonth: '#2dd4bf',
};

const indicators = ref([{
    id: 'last-fifteen-minutes-bar-chart',
    alerts: props.alertsEveryFifteenMinutes,
    categories: props.alertsEveryFifteenMinutesCategories,
    title: translate('Last 15 minutes'),
    backgroundColor: backgroundColors.everyFifteenMinutes,
    icon: 'clock',
}, {
    id: 'last-hour-bar-chart',
    alerts: props.alertsEveryHour,
    categories: props.alertsEveryHourCategories,
    title: translate('Last hour'),
    backgroundColor: backgroundColors.everyHour,
    icon: 'clock',
}, {
    id: 'last-four-hours-bar-chart',
    alerts: props.alertsEveryFourHours,
    categories: props.alertsEveryFourHoursCategories,
    title: translate('Last 4 hours'),
    backgroundColor: backgroundColors.everyFourHours,
    icon: 'clock',
}, {
    id: 'everyday-bar-chart',
    alerts: props.alertsEveryday,
    categories: props.alertsEverydayCategories,
    title: translate('Today'),
    backgroundColor: backgroundColors.everyHour,
    icon: 'calendar',
}, {
    id: 'every-week-bar-chart',
    alerts: props.alertsEveryWeek,
    categories: props.alertsEveryWeekCategories,
    title: translate('This week'),
    backgroundColor: backgroundColors.everyWeek,
    icon: 'calendar',
}, {
    id: 'every-month-bar-chart',
    alerts: props.alertsEveryMonth,
    categories: props.alertsEveryMonthCategories,
    title: translate('This month'),
    backgroundColor: backgroundColors.everyMonth,
    icon: 'calendar',
}]);
</script>

<template>
    <AlertsIndicator v-for="indicator in indicators"
        :indicator="indicator" />
</template>