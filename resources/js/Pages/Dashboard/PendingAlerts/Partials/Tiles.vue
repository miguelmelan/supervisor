<script setup>
import { ref, inject, computed } from 'vue';
import PendingAlertsAggregatedIndicator from '../Charts/PendingAlertsAggregatedIndicator.vue';
import PendingAlertsIndicator from '../Charts/PendingAlertsIndicator.vue';

const translate = inject('translate');

const props = defineProps({
    pendingAlertsCount: Number,
    alertsAverageResolutionTimeEveryday: Object,
    alertsAverageResolutionTime: String,
    alertsByCategory: Object,
});

const alertsAverageResolutionTime = computed(() => props.alertsAverageResolutionTime);

const pendingAlertsCount = computed(() => props.pendingAlertsCount);
const alertsByCategory = computed(() => props.alertsByCategory);

const selectColors = (number) => {
    const hue = number * 137.508; // use golden angle approximation
    return `hsl(${hue},50%,75%)`;
}
const generateColors = (count) => {
    return Array.from({ length: count }, (element, index) => selectColors(index));
}
const indicatorsBackgroundColors = {
    severity: {
        warn: '#faaf14',
        error: '#f5222d',
        fatal: '#7f1d1d',
    },
    type: (count) => generateColors(count),
    component: (count) => generateColors(count),
};

const indicators = ref([{
    id: 'by-severity',
    type: 'doughnut',
    alerts: alertsByCategory.value.severity,
    categories: alertsByCategory.value.severity.map(i => i.severity),
    key: 'severity',
    title: translate('By severity'),
    backgroundColor: alertsByCategory.value.severity.map(i => indicatorsBackgroundColors.severity[i.severity.toLowerCase()]),
    icon: 'rectangle-group',
}, {
    id: 'by-type',
    type: 'doughnut',
    alerts: alertsByCategory.value.notificationName,
    categories: alertsByCategory.value.notificationName.map(i => i.notification_name),
    key: 'notification_name',
    title: translate('By type'),
    backgroundColor: indicatorsBackgroundColors.type(alertsByCategory.value.notificationName.length),
    icon: 'rectangle-stack',
}, {
    id: 'by-component-type',
    type: 'doughnut',
    alerts: alertsByCategory.value.component,
    categories: alertsByCategory.value.component.map(i => i.component),
    key: 'component',
    title: translate('By component type'),
    backgroundColor: ['#a78bfa', '#38c6f4', '#f472b6'],
    icon: 'puzzle-piece',
}]);
</script>

<template>
    <PendingAlertsAggregatedIndicator :value="pendingAlertsCount.toString()" :title="__('Pending alerts count')"
        :color="pendingAlertsCount > 0 ? 'red' : 'blue'" />

    <PendingAlertsAggregatedIndicator :value="alertsAverageResolutionTime" value-size="2xl"
        :title="__('Average resolution time')" color="blue" />

    <PendingAlertsIndicator v-for="indicator in indicators" :indicator="indicator" />
</template>