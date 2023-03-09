<script setup>
import { onBeforeMount, ref } from 'vue';

const props = defineProps({
    modelValue: Array,
});

const tags = ref([]);

const emit = defineEmits(['update:modelValue']);

const handleTagsUpdated = () => {
    emit('update:modelValue', tags);
};

onBeforeMount(() => {
    tags.value = props.modelValue;
});
</script>

<template>
    <BaseTagsInput element-id="tags" v-model="tags" :placeholder="__('Type a tag')" :typeahead="true"
        :typeahead-url="'/tags?search=:search'" :discard-search-text="__('Cancel')" id-field="slug" text-field="name"
        :typeahead-hide-discard="true" @tags-updated="handleTagsUpdated">
        <template v-slot:selected-tag="{ tag, index, removeTag }">
            <div class="flex justify-between items-center">
                <span v-html="tag.name"></span>
                <a href="#" @click.prevent="removeTag(index)" class="ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </template>
    </BaseTagsInput>
</template>