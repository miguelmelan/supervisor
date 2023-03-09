<script setup>
import 'flowbite';
import Head from './Head.vue';
import Body from './Body.vue';

const props = defineProps({
    alerts: Object,
    selected: Array,
    selectAll: Boolean,
    sorting: Object,
    sort: Function,
    edit: Function,
    remove: Function,
});
</script>

<template>
    <div class="mt-6">
        <table class="min-w-full divide-y divide-y-gray-200 text-sm text-left text-gray-500">
            <Head :alerts="alerts" :select-all="selectAll" :sorting="sorting" :sort="sort" />
            <Body :alerts="alerts" :selected="selected" :edit="edit" :remove="remove" />
        </table>
    </div>
</template>