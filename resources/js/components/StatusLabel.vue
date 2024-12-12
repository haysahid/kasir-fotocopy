<script setup>
const props = defineProps({
    name: {
        type: String,
        default: "label",
    },
    status: {
        type: String,
        default: "normal",
    },
    autoScale: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <p
        class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-opacity-10 dark:bg-opacity-60"
        :class="{
            'max-md:text-xs max-md:px-2 max-md:py-0.5': props.autoScale,
            'bg-primary text-primary dark:text-white dark:text-opacity-80':
                props.status === 'normal',
            'bg-warning text-warning dark:text-white dark:text-opacity-80':
                props.status === 'warning',
            'bg-danger text-danger dark:text-white dark:text-opacity-80':
                props.status === 'danger',
            'bg-success text-success dark:text-white dark:text-opacity-80':
                props.status === 'success',
        }"
    >
        {{ props.name }}
    </p>
</template>
