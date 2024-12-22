<script setup>
const props = defineProps({
    pageTitle: String,
});
</script>

<template>
    <div class="flex flex-col items-start gap-3 md:items-center md:flex-row">
        <h2 class="w-full font-medium text-gray-900 text-title-sm2 dark:text-gray-200">
            {{ props.pageTitle }}
        </h2>

        <slot> </slot>
    </div>
</template>
