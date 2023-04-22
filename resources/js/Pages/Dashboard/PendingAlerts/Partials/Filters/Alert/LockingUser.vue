<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { computed } from 'vue';

const props = defineProps({
    values: Array,
    selectedValues: Array,
});

const emit = defineEmits([
    'updated',
]);

const selectedValues = computed(() => props.selectedValues);

const toggle = (value) => {
    if (selectedValues.value && selectedValues.value.includes(value)) {
        selectedValues.value.splice(selectedValues.value.indexOf(value), 1);
    } else {
        selectedValues.value.push(value);
    }
    emit('updated');
};

const find = (id) => {
    return props.values.find(v => v.id + '' === id);
}
</script>

<template>
    <div class="p-4">
        <InputLabel :value="__('Locked by')" />
        <Dropdown align="right" width="full" :contentClasses="['py-0', 'bg-gray-50', 'border', 'border-gray-300']" class="mt-1">
            <template #trigger>
                <button type="button"
                    class="inline-flex text-left items-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md w-full p-2.5 focus:outline-none transition">
                    {{ __('Select users')}}
                </button>
            </template>
            <template #content>
                <div class="w-full">
                    <DropdownLink v-for="value in values"
                        as="a" @click.prevent="toggle(value.id + '')"
                        href="#" icon-position="right" :withBottomBorder="true">
                        {{ value.name + ' - ' + value.email }}
                        <span v-if="$page.props.user.id === value.id">&nbsp;({{ __('you') }})</span>
                        <template #icon>
                            <svg v-if="selectedValues.includes(value.id + '')" aria-hidden="true" class="text-error-50" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </template>
                    </DropdownLink>
                </div>
            </template>
        </Dropdown>
        <div v-if="selectedValues && selectedValues.length > 0" class="mt-2">
            <span v-for="value in selectedValues" class="inline-flex items-center bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2 py-0.5 rounded border border-gray-500">
                {{ find(value).name + ' - ' + find(value).email }}
                <span v-if="$page.props.user.id == value">&nbsp;({{ __('you') }})</span>
                <button @click.prevent="toggle(value)" type="button" class="inline-flex items-center p-0.5 ml-2 text-sm text-gray-400 bg-transparent rounded-sm hover:bg-gray-200 hover:text-gray-900" :aria-label="__('Remove')">
                    <svg aria-hidden="true" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">{{ __('Remove') }}</span>
                </button>
            </span>
        </div>
    </div>
</template>