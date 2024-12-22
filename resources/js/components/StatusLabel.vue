<script setup>
const props = defineProps({
    name: {
        type: String,
        default: "label",
    },
    status: {
        type: String,
        default: "info",
    },
    autoScale: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <p
        class="inline-flex px-3 py-1 text-xs font-medium text-gray-500 bg-gray-500 rounded-full dark:text-gray-200 bg-opacity-10 dark:bg-opacity-60 dark:text-opacity-80 text-nowrap"
        :class="{
            'max-md:text-xs max-md:px-2 max-md:py-0.5': props.autoScale,
            'bg-primary text-primary dark:text-gray-200 dark:text-opacity-80':
                props.status === 'normal',
            'bg-warning text-warning dark:text-gray-200 dark:text-opacity-80':
                props.status === 'warning',
            'bg-danger text-danger dark:text-gray-200 dark:text-opacity-80':
                props.status === 'danger',
            'bg-success text-success dark:text-gray-200 dark:text-opacity-80':
                props.status === 'success',
        }"
    >
        {{ props.name }}
    </p>
</template>
