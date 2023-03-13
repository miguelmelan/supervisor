<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    range: Array,
});

const emit = defineEmits([
    'updated',
]);

const setRange = () => {
    emit('updated', props.range);
};

const startTime = ref([
    { hours: 0, minutes: 0},
    { hours: 0, minutes: 0},
]);
</script>

<template>
    <div class="p-4">
        <InputLabel :value="__('Creation date')" />
        <Datepicker v-model="range" range :placeholder="__('Select date range')"
            :max-date="Date()" :start-time="startTime" :month-change-on-scroll="false"
            :locale="$page.props.locale.code" :format="`${$page.props.locale.dateFormat} ${$page.props.locale.timeFormat}`"
            :cancel-text="__('Cancel')" :select-text="__('Select')" @update:modelValue="setRange"
            class="mt-1" />
    </div>
</template>